if (!App.Mixins) {
    App.Mixins = {};
}

App.Mixins.DeathReason = {
    data: function() {
        return {
            reasonValue: '',
            deathKitId: null,
            type: 'kit',
            deathBreederId: null,
            reasons: [],

            deathReasons: [],
            newDeathReason: '',
            addNew: false
        };
    },
    events: {
        kitDied: function(kit) {
            console.log('kit death event!');
            this.deathKitId = kit.id;
            this.reasonValue = '';
            this.type = 'kit';
            $('#death-reason-modal').modal('show');
        },

        breederDied: function(id) {
            console.log('breeder death event!');
            this.deathBreederId = id;
            this.reasonValue = '';
            this.type = 'breeder';
            $('#death-reason-modal').modal('show');
        },
    },
    methods: {
        sendReasonValue: function() {
            if(this.type == 'kit'){
                api.postKitDeathReasonValue(this.deathKitId, this.reasonValue);
            }
            if(this.type == 'breeder'){
                api.postBreederDeathReasonValue(this.deathBreederId, this.reasonValue);
            }
            $('#death-reason-modal').modal('hide');
        },

        initTypeahead: function() {
            api.getDeathReasons().then(data => {
                this.reasons = data.reasons;

                console.log($(this.$els.reasonValue))
                $(this.$els.reason).typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                }, {
                    source: (req, callback) => {
                        callback(this.reasons.filter(function (name) {
                            return name.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                        }));
                    }
                });
            });
        },
        // showDeathReasonModal: function () {
        //     console.log(this.type);
        //     $('#died-modal-' + this.type).modal('hide');
        //     $('#death-reason-modal').modal('show');
        // },
        getDeathReasons: function () {
            this.$http.get('/admin/users/getDeathReasonsList').then(response => {
                this.deathReasons = response.data.reasons;
            });
        },
        addNewReason: function () {
            this.$http.post('/admin/users/saveDeathReasonsList',
                {
                    name: this.newDeathReason
                })
                .then(() => {
                        this.getDeathReasons();
                        this.reasonValue = this.newDeathReason;
                        this.newDeathReason = '';
                        this.addNew = false;
                    },
                    response => {
                        this.errors = response.data;
                    }
            );
        },
        reasonCancel: function () {
            this.newDeathReason = '';
            this.addNew = false;
        },
        addNewFunc: function () {
            this.addNew = true;
        }
    },
    ready: function () {
        this.$nextTick(() => this.initTypeahead());

        this.getDeathReasons();
    },
};
