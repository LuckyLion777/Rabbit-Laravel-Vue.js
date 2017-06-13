App.Components.LitterBox = {
    template: "#litter-box-template",
    data: function () {
        return {
            kits: [],
            died: []
        }
    },
    props: ['litter', 'activeLitter'],
    components: {
    },
    computed: {
        age_weeks: function () {
            var born = App.parseMoment(this.litter.born);
            var now = moment().startOf('day');
            var age;
            if (this.litter.butchered_at) {
                var butchered = moment(this.litter.butchered_at, 'YYYY-MM-DD H:i:s');
                age = butchered.diff(born, "days");
            } else {
                age = now.diff(born, "days");
            }

            var modulo = age % 7;
            var weeks = 0;
            if(modulo === 0) {
                weeks = age / 7;
            } else {
                weeks = (age / 7).toFixed(1);
            }

            if (age < 7) {
                return "Less than a week";
            } else {
                return weeks + " week" + (weeks < 2 ? "" : "s");
            }
        },
        age_days: function () {
            var born = App.parseMoment(this.litter.born);
            var now = moment().startOf('day');
            var days;
            if (this.litter.butchered_at) {
                var butchered = moment(this.litter.butchered_at, 'YYYY-MM-DD H:i:s');
                days = butchered.diff(born, "days");
            } else {
                days = now.diff(born, "days");
            }

            return days <= 0 ? "Today" : days + ' days';
        }
    },
    methods: {
        getGenderClass: function (kit) {
            if (kit.sold_at) {
                return 'bg-green';
            }
            if (this.isArchived(kit)) {
                return "bg-gray";
            }
            if(!kit.sex || this.isButchered(kit)) {
                return "bg-gray-active";
            }
            if(kit.improved == 1) {
                return "bg-gray-active";
            }
            return kit.sex == "buck" ? "bg-aqua" : "bg-maroon";
        },
        getKits: function () {
            api.getLitterKits(this.litter.id).then(kits => {
                this.kits = kits;
                var alive = 0;
                $.each(kits, function (i, val) {
                    if( (val.alive == 1 || val.improved == 1) && val.archived == 0) {
                        alive++;
                    }
                });
                this.$dispatch('alive-kits', alive);
            });
            api.getLitterDied(this.litter.id).then(died => {
                this.died = died;
            });
        },
        showWeights: function (kit) {
            if(kit.weight_unit == "Pound/Ounces"){
                return kit.weight_slug_array.join(' - ');
            }
            return kit.weight.join(' - ');
        },

        editKitModal: function (kit) {
            this.$dispatch('edit-kit', kit);
        },

        isButchered: function (kit) {
            return kit.alive == 0 && kit.survived == 1 && kit.improved == 0 && kit.archived != 1;
        },

        isArchived: function (kit) {
            return kit.archived == 1;
        },

        revive: function(kit) {
            api.reviveKit(kit).then(() => {
                console.log('dispatching refresh kits');
                this.$dispatch('refresh-kits');
            });
        }
    },
    ready: function () {
        this.getKits();

        this.$on('refresh-kits-global', function (id) {
            if(id == this.litter.id) {
                this.getKits();
            }
        });
    }

};
