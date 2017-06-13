App.Components.LitterWeight = {
    template: "#litter-weight-template",
    data: function () {
        return {
            bucks: [],
            does: [],
            errors: {},
            date: moment(new Date).format(App.dateFormat),
            activeKit: {},
            loaded: 0,
            current: 0,
            showNavigator: 1,

            colors: [],
            weights: [],

            generate: false,
            listen: 0,
            changed: -1,
            selectedLitter: 0,
            weighedKits: 0
        }
    },
    props: ['litters', 'litter', 'kits'],
    components: {
        'image-upload': App.Components.ImageUpload,
        'sex-select': App.Components.SexSelect,
        'lbs-oz-input': App.Components.LbsOzInput
    },
    computed: {
        first: function () {
            return this.litter.id && !parseFloat(this.litter.total_weight);
        }
    },
    watch: {
        litter: function () {
            if(this.litter && this.litter.id) {
                api.getLitterKitsWeighOfType(this.litter.id, 'rabbitkit').then(kits => {
                    this.kits = kits;
                });
                this.selectedLitter = this.litter.id;
                this.current = 0;
            }
        },
        selectedLitter: function (a, b) {
            this.litter = _.find(this.litters, function (item) {
                return item.id == this.selectedLitter;
            }.bind(this));
        },
        kits: function () {
            var self = this;
            this.activeKit = this.kits[this.current];
            this.loaded = 1;
            this.weighedKits = 0;
            if(this.first) {
                $.each(this.kits, function (i, kit) {
                    if (kit.current_weight) {
                        self.weighedKits++;
                    }
                });
            }
        },

        current: function () {
            if(this.kits[this.current]) {
                this.activeKit = this.kits[this.current];
            } else {
                this.current = this.kits.length-1;
            }
        }
    },
    methods: {
        getGenderClass: function (sex) {
            if (!sex) {
                return "bg-gray-active";
            }
            return sex == "buck" ? "bg-aqua" : "bg-maroon";
        },

        generateIds: function () {
            var self = this;
            if(this.current == 0 && this.generate){
                $.each(this.kits, function (i) {
                    if(i){
                        this.given_id = isNaN(parseInt(self.kits[0].given_id)) ? self.kits[0].given_id + i: parseInt(self.kits[0].given_id) + i;
                    }
                });
            }
        },

        showWeights: function (kit) {
            if(kit) {
                var weight = '';

                if (kit.weight) {
                    if (kit.weight_unit == "Pound/Ounces") {
                        return kit.weight_slug_array.join(' - ');
                    }
                    return kit.weight.join(' - ');
                } else {
                    if (kit.weight_unit == "Pound/Ounces") {
                        return this.calcLbsOunces(kit.current_weight);
                    }
                    return kit.current_weight;
                }
            }
        },

        calcLbsOunces: function(weight){
            var lbs = parseFloat(weight/16);
            var tmp = lbs.toString().split('.');
            var txt = tmp[0] > 0 ? tmp[0] + " lbs " : '';
            var tmp2 = (lbs - tmp[0]) * 16;
            txt += tmp2 > 0 ? tmp2 + ' oz' : '';
            return txt;
        },

        saveAll: function () {

            if(this.first && this.changed) {
                this.saveKit(_.extend({}, this.activeKit), this.sendKits.bind(this));
            } else {
                this.sendKits();
            }

        },

        sendKits: function () {
            this.errors = {};

            var data = {
                date: this.date,
                kits: this.kits,
                kits_weighed: this.weighedKits
            };

            _.each(this.kits, function(kit){
                if(kit.weight_unit == "Pound/Ounces") {
                    if (kit.current_weight[0]) {
                        kit.current_weight = parseFloat(kit.current_weight[0] * 16) + (kit.current_weight[1] ? parseFloat(kit.current_weight[1]) : 0);
                        kit.weight.push(kit.current_weight)
                    }
                }
            });

            api.postLitterWeigh(this.litter, data).then(() => {
                this.$dispatch('refresh-kits');
                this.$dispatch('weighted');
                this.$dispatch('reload-tasks', {typeOfTask: 'all', page: null});
                $('#litter-weight-modal').modal('hide');
                this.refreshForm();
            }, response => {
                    this.$dispatch('refresh-kits');
                    this.$dispatch('weighted');
                    $('#litter-weight-modal').modal('hide');
                    this.errors = response.errors;
            });
        },

        sendKitsFirstWeigh: function () {
            this.errors = {};

            var data = {
                date: this.date,
                kits: this.kits,
                first: true
            };


            api.postLitterWeigh(this.litter, data).then(() => {
                this.$dispatch('refresh-kits');
                this.$dispatch('weighted');
                this.$dispatch('reload-tasks', {typeOfTask: 'all', page: null});
                $('#litter-weight-modal').modal('hide');
                this.litter.total_weight = 0;
                $.each(this.kits, (i, kit) => {
                    if (kit.current_weight) {
                        this.litter.total_weight += kit.current_weight;
                    }
                });
                this.refreshForm();
            }, response => {
                this.$dispatch('refresh-kits');
                this.$dispatch('weighted');
                $('#litter-weight-modal').modal('hide');
                this.errors = response.data;
            });
        },

        refreshForm: function () {
            this.selectedLitter = 0;
            this.litter = {};
        },

        diedKit: function (kit) {
            $('#kit-'+kit.id).fadeOut(500);
            this.kits = _.reject(this.kits, function(_kit){ return _kit.id == kit.id; });
            api.dieKit(kit, {'default_death_reason': true});
            return false;
        },

        setActiveKit: function (kit, index) {
            if(this.first) {
                this.changed && this.saveKit( _.extend({}, this.activeKit) );
                this.changed = -1;

                this.activeKit = kit;
                this.current = index;
            }
        },


        saveWeight: function (kit) {
            kit.current_weight_date = this.getTimestamp(this.date);
            $('#kit-'+kit.id).fadeOut(500);
            //this.saveKit(kit);
            this.weighedKits++;
        },

        saveKit: function (kit, callback) {
            var _kit = kit || _.extend({}, this.activeKit);

            if (_kit.weight == null) {
                _kit.weight = [];
            }
            _kit.weight.push(_kit.current_weight);

            var _current_weight_date = this.getTimestamp(this.date);
            _kit.current_weight_date = _current_weight_date;
            this.activeKit.current_weight_date = _current_weight_date;

            _kit.return_count = 1;
            var self = this;
            self.weighedKits = 0;
            $.each(self.kits, function (i, kit) {
                if (kit.current_weight) {
                    self.weighedKits++;
                }
            });
            if (this.kits.length == this.weighedKits) {
                this.sendKitsFirstWeigh();
            }
        },


        prevKit: function () {
            if(this.kits.length > 1) {
                this.activeKit.current_weight && this.changed && this.saveKit();
            }
            this.changed = -1;

            this.current--;
        },
        nextKit: function () {
            if(this.kits.length > 1) {
                this.activeKit.current_weight && this.changed && this.saveKit();
            }
            this.changed = -1;
            this.generateIds();

            if(this.current+1 >= this.kits.length) {
                this.current = 0;
            } else {
                this.current++;
            }
        },

        diedNextKit: function () {
            this.diedKit(this.activeKit);
            if(this.current >= this.kits.length) {
                this.current = 0;
            }
            this.changed = -1;
        },

        imageUploaded: function (image) {
            this.activeKit.image = image;
        },

        father: function () {
            return _.find(this.litter.parents, function (item) { return item.sex == "buck" });
        },
        mother: function () {
            return _.find(this.litter.parents, function (item) { return item.sex == "doe" });
        },

        getTimestamp: function getTimestamp(dateString) {
            if (App.dateFormat == 'DD/MM/YYYY') {
                var dateParts = dateString.split("/");
                dateString = dateParts[1] + '/' + dateParts[0] + '/' + dateParts[2];
            }
            return new Date(dateString).getTime();
        },

        initTypeahead: function() {
            api.getKitsAutocomplete().then(data => {
                this.colors = data.color;
                this.weights = data.weight;
            });
        }

    },
    ready: function () {
        this.initTypeahead();
    }

};
