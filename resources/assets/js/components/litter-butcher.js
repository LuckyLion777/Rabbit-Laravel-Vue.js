App.Components.LitterButcher = {
    template: "#litter-butcher-template",
    data: function () {
        return {
            errors: {},
            date: moment(new Date).format(App.dateFormat),
            loaded: 0,
            current: 0,
            selectedLitter: 0
        }
    },
    props: ['litters', 'litter', 'kits'],
    components: {
        'lbs-oz-input': App.Components.LbsOzInput
    },
    watch: {
        litter(litter, old_litter) {
            if (litter) {
                this.selectedLitter = litter.id;
                $(this.$els.checkall).prop('checked', false);
                if (this.selectedLitter) {
                    api.getLitterKitsWeighOfType(this.selectedLitter, 'rabbitkit').then(kits => {
                        this.kits = kits;
                    });
                }
            }
        },
        selectedLitter(selected, oldSelected) {
            if (oldSelected || (this.litter && !this.litter.id)) {
                this.litter = _.find(this.litters, item => {
                    return item.id == selected;
                });
            }
        }
    },
    methods: {
        getGenderClass(sex) {
            if (!sex) {
                return "bg-gray-active";
            }
            return sex == "buck" ? "bg-aqua" : "bg-maroon";
        },
        sendToButcher() {
            var selectedKits = _.filter(this.kits, kit => kit.selected);

            var data = {
                kits: selectedKits,
                date: this.date,
                litter_id: this.selectedLitter
            };

            api.butcherKits(data).then(() => {
                this.$dispatch('refresh-kits');
                this.$dispatch('litter-archived', {litter: this.selectedLitter})
                console.log('submitted butch');
                if (selectedKits.length) {
                    // #69
                    console.log('dispatching litterButchered');
                    this.$dispatch('litterButchered', this.selectedLitter);
                }
            });

            $('#litter-butcher-modal').modal('hide');
        }

    },
    ready() {

        $(this.$els.checkall).on('click', function () {
            if(this.checked) {
                $('.js_butcher_checkbox').prop('checked', false).trigger('click');
            } else {
                $('.js_butcher_checkbox').prop('checked', true).trigger('click');
            }
        });

    }

};
