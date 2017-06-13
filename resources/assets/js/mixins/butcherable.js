if (!App.Mixins) {
    App.Mixins = {};
}

App.Mixins.Butcherable = {
    data: function() {
        return {
            butcherValue: '',
            butcheredLitterId: null,
            butcherType: 'litter',
            butcheredBreederId: null
        };
    },
    events: {
        litterButchered: function(id) {
            console.log('litterButchered event!');
            this.butcheredLitterId = id;
            this.butcherValue = '';
            this.butcherType = 'litter';
            $('#litter-butcher-value').modal('show');
        },

        breederButchered: function(id) {
            console.log('breederButchered event!');
            this.butcheredBreederId = id;
            this.butcherValue = '';
            this.butcherType = 'breeder';
            $('#litter-butcher-value').modal('show');
        }
    },
    methods: {
        sendButcherValue: function() {
            if(this.butcherType == 'litter'){
                api.postLitterButcherValue(this.butcheredLitterId, this.butcherValue);
            }
            if(this.butcherType == 'breeder'){
                api.postBreederButcherValue(this.butcheredBreederId, this.butcherValue);
            }
            $('#litter-butcher-value').modal('hide');
        }
    }
};
