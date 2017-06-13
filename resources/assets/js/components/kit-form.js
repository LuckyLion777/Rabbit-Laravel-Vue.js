App.Components.KitForm = App.Components.Exploitable.Section.extend({
    template: "#kit-form-template",
    data: function () {
        return {
            modalUnique: 'kit',
            loading: 1,
            saver: api.saveKit,
            archiver: api.archiveKit,
            unarchiver: api.unarchiveKit,
            deleter: api.deleteKit,
            dier: api.dieKit,
            seller: api.sellKit,
            unseller: api.unsellKit,
            transferer: api.transferKit
        }
    },
    props: ['kit', 'litter'],
    watch: {
        kit: function () {
            $('.js_icheck-kit-blue, .js_icheck-kit-red').iCheck('update');
            $(this.$els.color).typeahead('val', this.kit.color);
        }
    },
    components: {
        'image-upload': App.Components.ImageUpload,
        'lbs-oz-input': App.Components.LbsOzInput
    },
    computed: {
        kit_weight: function () {
            return _.map(this.kit.weight, function (val, i) {
                var formatted_date = "&nbsp;";
                if (this.kit.weight_date != null && this.kit.weight_date[i] != undefined && this.kit.weight_date[i] != 'null') {
                    formatted_date = moment(new Date(this.kit.weight_date[i])).format(App.dateFormat);
                }
                // var formatted_date = (this.kit.weight_date[i] == undefined || this.kit.weight_date[i] == 'null') ? '&nbsp;' : moment(new Date(this.kit.weight_date[i])).format(App.dateFormat);
                return {id: i, value: val, wdate: formatted_date};
            }.bind(this));
        },
        models: function() {
            return this.litter;
        }
    },
    events: {
        archivedModel: function(kit) {
            this.$dispatch('refresh-kits', kit);
            $('#kit-form-modal').modal('hide');
        },
        deletedModel: function(kit) {
            this.$dispatch('refresh-kits', kit);
            $('#kit-form-modal').modal('hide');
        },
        diedModel: function(kit) {
            this.$dispatch('refresh-kits', kit);
            $('#kit-form-modal').modal('hide');
            this.$dispatch('kitDied', kit);
        },
        soldModel: function(kit) {
            this.$dispatch('refresh-kits', kit);
            $('#kit-form-modal').modal('hide');
        },
        unsoldModel: function(kit) {
            this.$dispatch('refresh-kits', kit);
            $('#kit-form-modal').modal('hide');
        }
    },
    methods: {
        kitDied: function (kit) {
            api.dieKit(kit).then(() => {
                this.$dispatch('refresh-kits', kit);
            });
            $('#kit-form-modal').modal('hide');
        },

        makeBreeder: function (kit) {
            kit._method = "PUT";
            kit.weight_changed = 1;
            api.saveKit(kit).then(() => {
                api.makeBreeder(kit).then(res => {
                    $('#kit-form-modal').modal('hide');
                    if (res.id) {
                        this.$router.go({path: '/profile/' + res.id});
                    }
                });
            });
        },

        unarchive: function (kit) {
            api.unarchiveKit(kit).then(() => {
                this.$dispatch('refresh-kits', kit);
            });
            $('#kit-form-modal').modal('hide');
        },

        saveKit: function (_kit) {
            this.loading = 1;
            var kit = _.extend({}, _kit);

            kit.weight_changed = 1;
            if (kit.new_weight) {
                if (kit.weight == null) {
                    kit.weight = [];
                }
                kit.weight.push(kit.new_weight);

                var new_weight_date = Date.now();
                if (kit.weight_date == null) {
                    kit.weight_date = [];
                }
                
                // Handle existing weights without date
                var diff_length = (kit.weight.length - 1) - kit.weight_date.length;
                for (var i = 1; i <= diff_length; i++) {
                    kit.weight_date.push('null');
                }
                kit.weight_date.push(new_weight_date);
            }

            api.saveKit(kit).then(() => {
                this.loading = 0;
                this.$dispatch('refresh-kits', kit);
            });

            $('#kit-form-modal').unbind('hide.bs.modal').modal('hide');
        },

        father: function () {
            return _.find(this.litter.parents, function (item) {
                return item.sex == "buck"
            });
        },
        mother: function () {
            return _.find(this.litter.parents, function (item) {
                return item.sex == "doe"
            });
        },

        initTypeahead: function() {
            this.loading = 1;
            api.getKitsAutocomplete().then(data => {
                this.loading = 0;
                var colors = data.color;
                $(this.$els.color).typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                }, {
                    source: function(req, callback) {
                        callback(colors.filter(function(color) {
                            return color.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                        }));
                    }
                });

            });
        }

    },
    ready: function () {

        this.initTypeahead();

        $('.js_icheck-kit-blue').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        }).on('ifChecked', function (event) {
            this.kit.sex = "buck";
        }.bind(this));

        //Red color scheme for iCheck
        $('.js_icheck-kit-red').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        }).on('ifChecked', function (event) {
            this.kit.sex = "doe";
        }.bind(this));
    },
    mixins: [App.Mixins.DeathReason]
});
