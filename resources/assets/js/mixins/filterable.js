if (!App.Mixins) {
    App.Mixins = {};
}

App.Mixins.Filterable = {
    data: function() {
        return {
            itemsLoaded: 1,
            disableLoadMore: false,
            filters: {
                //name: '',
                //breed: '',
                //cage: '',
                //color: '',
                //tattoo: '',
                //date_of_birth: {
                //    from: null,
                //    to: null
                //},
                //aquired: {
                //    from: null,
                //    to: null
                //}
            },
            filterRules: {
                //name: 'string',
                //breed: 'string',
                //cage: 'string',
                //color: 'string',
                //tattoo: 'string',
                //date_of_birth: 'daterange',
                //aquired: 'daterange'
            },
            totalItems: null,
            searchQuery: '',
            searchFields: [] //['name', 'breed', 'cage', 'color', 'tattoo', 'notes']
        };
    },
    watch: {
        filters: {
            handler: function() {
                this.itemsLoaded = 0;
                this.disableLoadMore = false;
                this.loadMore();
            },
            deep: true
        },
        searchQuery: function(){
            this.itemsLoaded = 0;
            this.disableLoadMore = false;
            this.loadMore();
        },
        filterRanges: {
            handler: function(newValue) {
                _.each(newValue, function(value, key){
                    if(value && value[0] && value[1]) {
                        this.filters[key]['from'] = value[0].format(App.dateFormat);
                        this.filters[key]['to'] = value[1].format(App.dateFormat);
                    } else {
                        this.filters[key]['from'] = this.filters[key]['to'] = null;
                    }
                }.bind(this));
            },
            deep: true
        }
    },

    methods: {
        itemFilter: function(item){
            var passed = true;
            var searchPassed = false;
            _.each(this.filters, function(value, key){
                // pregant filter
                if (passed && value !== null && this.filterRules[key] == 'bool') {
                    if (item.pregnant !== value || item.sex !== 'doe')
                        passed = false;
                }
                // pregant filter end
                if(passed && value && this.filterRules[key] == 'number'){
                    if(item[key]){
                        if(item[key] != value){
                            passed = false;
                        }
                    } else {
                        passed = false;
                    }
                }
                if(passed && value && this.filterRules[key] == 'string'){
                    var testKey = '' + item[key];
                    if(item[key]){
                        if(testKey.toLowerCase().indexOf(value.toLowerCase()) == -1){
                            passed = false;
                        }
                    } else {
                        passed = false;
                    }
                }
                if(passed && this.filterRules[key] == 'daterange'){
                    if(value.from) {
                        if (!item[key] || App.parseMoment(item[key]).isBefore(value.from)) {
                            passed = false;
                        }
                    }
                    if(value.to) {
                        if (!item[key] || App.parseMoment(item[key]).isAfter(value.to)) {
                            passed = false;
                        }
                    }
                }
                if(passed && this.filterRules[key] == 'daterangepicker'){
                    if(value[0]) {
                        if (!item[key] || App.parseMoment(item[key]).isBefore(value[0])) {
                            passed = false;
                        }

                        if (!item[key] || App.parseMoment(item[key]).isAfter(value[1])) {
                            passed = false;
                        }
                    }
                }
                if(passed && typeof this.filterRules[key] === 'object' && this.filterRules[key].type === 'relation'){
                    //var relationKey = this.filterRules[key].split('.');
                    var filter = this.filterRules[key];
                    //var relationName = this.filterRules[key].relationName;
                    //var relationFilterField = this.filterRules[key].relationFilterField;
                    //var relationField = this.filterRules[key].relationField;
                    if(value) {
                        if (item[filter.relationName] instanceof Array && !item[filter.relationName].length) {
                            passed = false;
                        } else {
                            var relationPassed = false;
                            if(item[filter.relationName] instanceof Array) {
                                _.each(item[filter.relationName], function (relationItem) {
                                    if(this.checkRelation(key, relationItem, filter, value)){
                                        relationPassed = true;
                                    }
                                }.bind(this));

                            } else {
                                relationPassed = this.checkRelation(key, item[filter.relationName], filter, value)
                            }
                            if (!relationPassed) {
                                passed = false;
                            }
                        }
                    }
                }
            }.bind(this));

            if(this.searchQuery) {
                _.each(this.searchFields, function (key) {
                    if (item[key] && '' + item[key].toString().toLowerCase().indexOf(this.searchQuery.toLowerCase()) != -1) {
                        searchPassed = true;
                    }
                }.bind(this));
            } else {
                searchPassed = true;
            }

            return passed && searchPassed;
        },

        resetFilter: function(){
            _.each(this.filters, function(value, key){
                if (value && (this.filterRules[key] == 'string' || this.filterRules[key] == 'number')) {
                    this.filters[key] = '';
                }
                if(this.filterRules[key] == 'daterange'){
                    this.filters[key].from = null;
                    this.filters[key].to = null;
                }
                if(this.filterRules[key] == 'bool'){
                    this.filters[key] = null;
                }
            }.bind(this));
            _.each(this.filterRanges, function(value, key){
                this.filterRanges[key] = null;
            }.bind(this));
        },

        mergeByProperty: function(arr1, arr2, prop) {
            _.each(arr2, function(arr2obj) {
                var arr1obj = _.find(arr1, function(arr1obj) {
                    return arr1obj[prop] === arr2obj[prop];
                });

                //If the object already exist extend it with the new values from arr2, otherwise just add the new object to arr1
                arr1obj ? _.extend(arr1obj, arr2obj) : arr1.push(arr2obj);
            });
        },

        loadMore: function(){
            //implement
        },

        checkRelation: function(key, relationItem, filter, value){
            if (filter.relationField) {
                if (relationItem[filter.relationField] == key && this.checkValue(value, filter, relationItem)) {
                    return true;
                }
            } else if (this.checkValue(value, filter, relationItem)) {
                return true;
            }
            return false;
        },

        checkValue: function(value, filter, relationItem){
            if (filter.valueType === 'number'){
                return relationItem[filter.relationFilterField] == value;
            }
            return relationItem[filter.relationFilterField].toLowerCase().indexOf(value.toLowerCase()) != -1
        }
    }
};
