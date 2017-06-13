App.Components.BreederButcher = {
    template: "#breeder-butcher-template",
    data: function () {
        return {
            errors: {},
            date: moment(new Date).format(App.dateFormat),
            loaded: 0,
            current: 0
        }
    },
    props: ['breeder'],
    components: {
        'lbs-oz-input': App.Components.LbsOzInput
    },
    watch: {

    },
    methods: {
        getGenderClass: function (sex) {
            if (!sex) {
                return "bg-gray-active";
            }
            return sex == "buck" ? "bg-aqua" : "bg-maroon";
        },
        sendToButcher: function () {

            var data = {
                breeder: this.breeder,
                date: this.date,
                butchered: 1
            };

            api.butcherBreeder(this.breeder, data).then(() => {
                this.breeder.butchered = true;
                this.breeder.butchered_at = this.date;
                console.log('submitted butch');
                this.$dispatch('breederButchered', this.breeder.id);

            });

            $('#breeder-butcher-modal').modal('hide');
        }

    },
    ready: function () {


    }

};
