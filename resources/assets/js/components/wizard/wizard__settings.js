App.Components.WizardSettings = {
    template: "#wizard__settings-template",
    data: function () {
        return {
            user: {},
            chains: [],
            user_id: "",
            success: {},
            errors: {},
            loaded: false,
            chainName: '',
            chainDays: '',
            chainIcon: '',
            iconBackground: {
                'bg-red': false, 'bg-blue': false, 'bg-maroon': false, 'bg-green': false, 'bg-yellow': false,
                'bg-grey': false, 'bg-purple' : false, 'fa-calendar' : false, 'fa-heart' : false,
                'fa-asterisk' : false, 'fa-bookmark' : false, 'fa-eye' : false, 'fa-flag' : false,
                'fa-medkit' : false, 'fa-paw' : false, 'fa-trophy' : false, 'fa-inbox' : false
            },
            loading: false
        }
    },
    props: [],
    components: {
        'settings-image-upload': App.Components.SettingsImageUpload
    },
    computed: {
        isSubscribed: function() {
            return App.isSubscribed;
        },
        isPremium: function() {
            return App.isPremiumSubscribed;
        }
    },
    watch:{

        'chainIcon': function(newValue, oldValue){

            if(newValue){
                var newClass = newValue.split(' ')[1];
                var oldClass = oldValue ? oldValue.split(' ')[1] : '';

                if(newClass == 'bg-gray') newClass = newValue.split(' ')[0];
                if(oldClass == 'bg-gray') oldClass = oldValue ? oldValue.split(' ')[0] : '';

                this.iconBackground[newClass] = true;
                if(oldValue) this.iconBackground[oldClass] = false;

            }
        },

        'digest_enabled': function(newValue, oldValue){
            $(this.$els.digest_enabled).iCheck('update');
            if(this.loaded) {
                if (newValue == false) {
                    this.temp_digest_day = this.user.digest_day;
                    this.user.digest_day = -1;
                } else {
                    this.user.digest_day = this.temp_digest_day;
                }
            }
        }
    },
    methods: {
        updateSettings: function () {
            this.success = {};
            this.errors = {};
            this.loading = true;
            api.postUserSettings(this.user.id, this.user).then(
                res => {
                    this.loading = false;
                    this.success = res;
                    this.$route.router.go('/wizard/breeders');
                },
                response => {
                    this.loading = false;
                    this.errors = response.errors;
                }
            );

        },
        addChain: function () {
            if(!this.chainName) {
                alert('You must specify Name');
                return false;
            }
            if(!this.chainDays) {
                alert('You must specify Days');
                return false;
            }
            var chain = {
                name: this.chainName,
                days: this.chainDays,
                icon: this.chainIcon,
                id: 'new_' + new Date().getTime()
            };

            this.chains.push(chain);
            this.chains = _.sortBy(this.chains, 'days');

            $('#new_chain').modal('hide');
            this.chainName = '';
            this.chainDays = '';


        },

        removeChain: function(id){
            $('li.' + id).remove();
            delete this.user.breedchains.icon[id];
            delete this.user.breedchains.days[id];
            delete this.user.breedchains.name[id];
            this.chains = this.chains.filter(function(chain) {
                return chain.id != id;
            });

        }
    },
    ready: function () {
        api.getCurrentUserSettingsData().then(data => {
            this.user = data.user;
            this.chains = data.chains;
            this.digest_enabled = this.user.digest_day != -1;

            this.$nextTick(function () {
                this.loaded = true;
            })
        });

        $(this.$els.digest_enabled).iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        }).on('ifChecked', function(event){
            this.digest_enabled = true;
        }.bind(this))
        .on('ifUnchecked', function(event){
            this.digest_enabled = false;
        }.bind(this));
    }
};
