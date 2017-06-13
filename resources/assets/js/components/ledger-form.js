App.Components.LedgerForm = App.Components.Exploitable.Form.extend({
    template: "#ledger-form-template",
    data: function() {
        return {
            category_names: [],
            categories: [],
            modal_id: '#ledger-form',
            emptyModel: {
                category_id: null,
                debit: true,
                description: '',
                category_name: ''
            },
            entry: {},
            litters: [],
            breeders: [],
            saver: api.saveLedgerEntry,
            loading: 1,
        };
    },
    props: ['associated_type', 'associated_id'],
    computed: {
        category: function() {
            return _.find(this.categories, function(category) {
                return this.entry.category_id == category.id;
            }.bind(this));
        },
        associated: function() {
            if (!this.category) return false;
            return ['breeder', 'litter'].indexOf(this.category.special) !== -1;
        },
        associated_record: function() {
            var options = this.category.special === 'breeder' ? this.breeders : this.litters;
            return options.find(function(option) {
                return option.id == this.associated_id;
            }.bind(this));
        },
        associations: function() {
            if (!this.category || ['breeder', 'litter'].indexOf(this.category.special) === -1) return [];
            return this[this.category.special + 's'];
        },
        default_category: function() {
            return this.categories.filter(function (category) {
                return category.special === 'general';
            })[0];
        },
        model: {
            get: function() {
                return this.entry;
            },
            set: function(model) {
                this.entry = model;
            }
        }
    },
    watch: {
        entry: function (entry) {
            if (!('debit' in entry)) {
                entry.debit = 1;
            }
            this.resetArchivedFilterBreeders();
            $('.js_icheck-ledger-green, .js_icheck-ledger-red').iCheck('update');
            $('#entry-name').typeahead('val', this.entry.name);
            $('#entry-amount').typeahead('val', this.entry.amount);
        },

        'entry.associated_id': function(value){
            this.setFilterBreedersEntry(value);
        }
    },
    methods: {
        loadCategories: function () {
            this.loading = 1;
            api.getLedgerCategories().then(data => {
                this.loading = 0;
                this.categories = data.categories;
                this.emptyModel.category_id = this.entry.category_id || this.default_category.id;
            });
        },
        loadLitters: function () {
            this.loading = 1;
            api.getLittersList().then(litters => {
                this.loading = 0;
                this.litters = litters.map(function(item) {
                    item.name = (item.parents[0] || {name: ''}).name + '/' + (item.parents[1] || {name: ''}).name;
                    return item;
                });
            });
        },
        loadBreeders: function () {
            this.loading = 1;
            api.getBreedersList().then(data => {
                this.loading = 0;
                this.breeders = data.bucks.concat(data.does);
            });
        },
        representAssociation: function(association) {
            return this.category.special === 'breeder'
                    ? association.name + ': ' + association.tattoo
                    : association.given_id + ': ' + association.name;
        },
        initTypeahead: function() {
            this.loading = 1;
            api.getLedgerAutocomplete().then(data => {
                this.loading = 0;
                var names = data.name,
                    amounts = data.amount;

                this.category_names = data.category_name;

                $('#entry-name').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                }, {
                    source: function(req, callback) {
                        callback(names.filter(function(name) {
                            return name.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                        }));
                    }
                });

                $('#entry-amount').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                }, {
                    source: function(req, callback) {
                        callback(amounts.filter(function(amount) {
                            return amount.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                        }));
                    }
                });
            });
        },
        filterEntriesBreeders: function (value) {
            if (this.withArchivedBreeders) {
                return true;
            }
            return this.entry.associated_id == value.id || value.archived === 0 && value.sold_at == null;
        }
    },
    events: {
        modalShown: function() {
            if (this.associated_type) {
                this.entry.category_id = this.categories.find(function(category) {
                    return category.special === this.associated_type;
                }.bind(this)).id;
                this.entry.associated_id = this.associated_id;
                this.setFilterBreedersEntry(this.entry.associated_id);
            }
        }
    },
    ready: function () {
        this.initTypeahead();
        this.loadCategories();
        this.loadLitters();
        this.loadBreeders();
        
        this.$watch('entry.category_id', function (val) {
            if (val) return;
            
            $('#entry-category-name').typeahead({
                hint: true,
                highlight: true,
                minLength: 0
            }, {
                source: function(req, callback) {
                    callback(this.category_names.filter(function(name) {
                        return name.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                    }));
                }.bind(this)
            });

            $('#entry-category-name').typeahead('val', this.entry.category_name);
        });

        $('.js_icheck-ledger-green').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        }).on('ifChecked', function(event){
            this.entry.debit = true;
        }.bind(this));

        //Red color scheme for iCheck
        $('.js_icheck-ledger-red').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        }).on('ifChecked', function(event){
            this.entry.debit = false;
        }.bind(this));
    },

    mixins: [App.Mixins.BreedersFilter]
});
