App.Components.BreederForm = {
    template: "#breeder-form-template",
    data: function () {
        return {
            bucks: [],
            does: [],
            categories: [],
            loading: 1,
            newBuck: {
                sex: "buck"
            },
            newDoe: {
                sex: "doe"
            },
            errors: {},
            warnings: {},
            breeds: [],
            emptyModel: {
                category_id: null,
                category_name: ''
            },
            weight_units: 'Pounds',
            mother_name: '',
            father_name: '',
            mother_input_focused: false,
            father_input_focused: false,
        }
    },
    props: ['breeder','breeders'],
    components: {
        'image-upload': App.Components.ImageUpload,
        'sex-select': App.Components.SexSelect,
        'lbs-oz-input': App.Components.LbsOzInput
    },
    watch: {
        breeder: function () {
            $('.js_icheck-breeder-blue, .js_icheck-breeder-red').iCheck('update');
            $('#breeder-cage').typeahead('val', this.breeder.cage);
            $('#breeder-tattoo').typeahead('val', this.breeder.tattoo);
            $('#breeder-color').typeahead('val', this.breeder.color);
            $('#breeder-breed').typeahead('val', this.breeder.breed);
            this.setFilterBreeders(this.breeder.mother_id, this.breeder.father_id);
            this.resetArchivedFilterBreeders();
        },

        'breeder.mother_id': function(){
            console.log('izmenenie');
            this.setFilterBreeders(this.breeder.mother_id, this.breeder.father_id);
            for (var i = 0; i < this.does.length; i++) {
                console.log('breder name: ', this.does[i].name);
                if (+this.does[i].id === +this.breeder.mother_id) {
                    console.log('zashlo');
                    this.mother_name = this.does[i].name;
                }
            }
            this.mother_input_focused = false;
        },

        'breeder.father_id': function(){
            console.log('izmenenie');
            this.setFilterBreeders(this.breeder.mother_id, this.breeder.father_id);
            for (var i = 0; i < this.bucks.length; i++) {
                console.log('breder name: ', this.bucks[i].name);
                if (+this.bucks[i].id === +this.breeder.father_id) {
                    console.log('zashlo');
                    this.father_name = this.bucks[i].name;
                }
            }
            this.father_input_focused = false;
        }
    },
    methods: {
        uniqueFieldSet: function(field){
            return _.unique(_.pluck(_.flatten([].concat(this.does, this.bucks)), field));
        },
        initModal: function () {
            this.loading = 1;
            this.weight_units = App.weight_units;
            api.getBreedersList().then(breeders => {
                this.loading = 0;
                this.bucks = breeders.bucks;
                this.does = breeders.does;

                for (var i = 0; i < this.bucks.length; i++) {
                    console.log('breder name: ', this.bucks[i].name);
                    if (+this.bucks[i].id === +this.breeder.father_id) {
                        console.log('zashlo');
                        this.father_name = this.bucks[i].name;
                    }
                }

                console.log('father name: ', this.father_name);

                for (var i = 0; i < this.does.length; i++) {
                    console.log('breder name: ', this.does[i].name);
                    if (+this.does[i].id === +this.breeder.mother_id) {
                        console.log('zashlo');
                        this.mother_name = this.does[i].name;
                    }
                }

                console.log('father name: ', this.mother_name);



                var breeds = this.uniqueFieldSet("breed");
                var tattooes = this.uniqueFieldSet("tattoo");
                var cages = this.uniqueFieldSet("cage");
                var colors = this.uniqueFieldSet("color");

                $('#breeder-breed').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                },
                {
                    source: this.substringMatcher(breeds)
                });

                $('#breeder-cage').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                },
                {
                    source: this.substringMatcher(cages)
                });


                $('#breeder-tattoo').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                },
                {
                    source: this.substringMatcher(tattooes)
                });
                $('#breeder-color').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                },
                {
                    source: this.substringMatcher(colors)
                });

            });
        },

        sendBreeder: function () {
            var breeder = this.breeder;
            this.loading = 1;
            api.saveBreeder(breeder).then(
                data => {
                    this.loading = 0;
                    App.vent.trigger('breederSaved');
                    $('#breeder-form').modal('hide');
                    if (breeder.id == 0) {
                        this.breeders.push(data);
                    } else {
                        var match = _.find(this.breeders, function (item) {
                            return item.id === breeder.id
                        });
                        if (match) {
                            _.extendOwn(match, data)
                        }
                        this.breeder = data;
                    }
                },
                response => {
                    this.loading = 0;
                    if (response.data === 'breeders-limit-exceeded') {
                        $('#breeder-form').modal('hide');
                        $('#breeder-limit-alert').modal('show');
                    }
                    this.errors = response.data;
                }
            );
        },

        checkDoubledId: function () {
            api.checkBreederId(this.breeder.tattoo).then(check => {
                if(check.idDoubled) {
                    this.warnings = { tattoo: ['Breeder ID is duplicated'] };
                } else {
                    this.warnings = {};
                }
            });
        },


        addNewBuck: function () {
            this.loading = 1;
            api.saveBreeder(_.extend({}, App.emptyBreeder, this.newBuck)).then(data => {
                this.loading = 0;
                if (data.id) {
                    this.bucks.push(data);
                    this.breeder.father_id = data.id;
                }
                this.newBuck = { sex: "buck" };
            });
        },

        addNewDoe: function () {
            this.loading = 1;
            api.saveBreeder($.extend({}, App.emptyBreeder, this.newDoe)).then(data => {
                this.loading = 0;
                if (data.id) {
                    this.does.push(data);
                    this.breeder.mother_id = data.id;
                }
                this.newDoe = { sex: "doe" };
            });
        },

            substringMatcher: function(strs) {
                return function findMatches(q, cb) {
                    var matches, substrRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function(i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        },
        loadCategories: function () {
            this.loading = 1;
            api.getBreederCategories().then(data => {
                this.loading = 0;
                this.categories = data.categories;
                this.emptyModel.category_id = this.breeder.category_id || this.categories.find(cat => cat.default).id;
            });
        },

        chooseParent: function (parent_id, id, parent_name, name) {
            console.log('jopa');
            console.log(parent_id, id, parent_name, name);
            parent_id = id;
            parent_name = name;
            console.log(parent_id, id, parent_name, name);
        }
    },
    ready: function () {
        this.initModal();




        this.loadCategories();
        App.vent.on('breederSaved', () => {
            this.loadCategories();
        });

        $('.js_icheck-breeder-blue').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        }).on('ifChecked', function(event){
            this.breeder.sex = "buck";
        }.bind(this));

        //Red color scheme for iCheck
        $('.js_icheck-breeder-red').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        }).on('ifChecked', function(event){
            this.breeder.sex = "doe";
        }.bind(this));


    },

    created: function () {

        if (this.breeder.mother_id)
            this.initName(this.breeders, this.breeder.mother_id, this.mother_name);

        if (this.breeder.father_id)
            this.initName(this.breeders, this.breeder.father_id, this.father_name);
    },

    mixins: [App.Mixins.BreedersFilter]

};
