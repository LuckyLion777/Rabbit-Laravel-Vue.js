App.Components.PedigreeForm = {
    template: "#pedigree-form-template",
    data: function () {
        return {
            bucks: [],
            does: [],
            newBuck: {
                sex: "buck"
            },
            newDoe: {
                sex: "doe"
            },
            errors: {},
            warnings: {},
            loading: 0,
        }
    },
    props: ['breeder','breeders'],
    components: {
        'image-upload': App.Components.ImageUpload,
        'lbs-oz-input': App.Components.LbsOzInput
    },
    computed: {
        weight_unit: function () {
            if (this.breeder.breeder) {
                return this.breeder.breeder.weight_unit;
            } else if (this.breeder.kit) {
                return this.breeder.kit.weight_unit;
            }
        }
    },
    watch: {
        breeder: function () {
            $('.js_icheck-breeder-blue, .js_icheck-breeder-red').iCheck('update');
            $('#pedigree-name').typeahead('val', this.breeder.name);
            $('#pedigree-color').typeahead('val', this.breeder.color);
            $('#pedigree-breed').typeahead('val', this.breeder.breed);
        }
    },
    methods: {
        uniqueFieldSet: function(field){
            return _.unique(_.pluck(_.flatten([].concat(this.does, this.bucks)), field));
        },

        initModal: function () {
            //App.initDatePicker();
            api.getBreedersList().then(breeders => {
                this.bucks = breeders.bucks;
                this.does = breeders.does;

                var breeds = this.uniqueFieldSet("breed");
                var names = this.uniqueFieldSet("name");
                var colors = this.uniqueFieldSet("color");

                $('#pedigree-breed').typeahead({
                        hint: true,
                        highlight: true,
                        minLength: 0
                    },
                    {
                        source: this.substringMatcher(breeds)
                    });

                $('#pedigree-name').typeahead({
                        hint: true,
                        highlight: true,
                        minLength: 0
                    },
                    {
                        source: this.substringMatcher(names)
                    });

                $('#pedigree-color').typeahead({
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
            this.loading = 1;
            this.breeder.weight_date = Date.now();
            var breeder = this.breeder;
            api.savePedigree(breeder).then(
                data => {
                    this.loading = 0;
                    $('#pedigree-form').modal('hide');
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
                    //this.closeModal();
                },
                response => {
                    this.loading = 0;
                    this.errors = response.errors;
                }
            )
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
                    this.newBuck = { sex: "buck" };
                }
            });
        },

        addNewDoe: function () {
            this.loading = 1;
            api.saveBreeder($.extend({}, App.emptyBreeder, this.newDoe)).then(data => {
                this.loading = 0;
                if (data.id) {
                    this.does.push(data);
                    this.breeder.mother_id = data.id;
                    this.newDoe = {sex: "doe"};
                }
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
        }
    },
    ready: function () {
        this.initModal();

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
    }

};
