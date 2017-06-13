App.Components.Settings = {
    template: "#settings-template",
    data: function () {
        return {
            user: {},
            chains: [],
            user_id: "",
            success: {},
            errors: {},
            loaded: false,
            digest_enabled: false,
            temp_digest_day: -1,
            success_general: {},
            success_schedule: {},
            success_pedigree: {},
            chainName: '',
            chainDays: '',
            chainIcon: '',
            iconBackground: {
                'bg-red': false, 'bg-blue': false, 'bg-maroon': false, 'bg-green': false, 'bg-yellow': false,
                'bg-grey': false, 'bg-purple' : false, 'fa-calendar' : false, 'fa-heart' : false,
                'fa-asterisk' : false, 'fa-bookmark' : false, 'fa-eye' : false, 'fa-flag' : false,
                'fa-medkit' : false, 'fa-paw' : false, 'fa-trophy' : false, 'fa-inbox' : false
            },

            supports_notifications: false,
            notifications_disabled: false,
            notifications_subscribed: false
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
        updateSettings: function (from) {
            this.success = {};
            this.errors = {};
            this.success_general = {};
            this.success_pedigree = {};
            this.success_schedule = {};
            api.postUserSettings(this.user_id, this.user).then(
                res => {
                    if (from=='general') {
                        this.success_general = res;
                        App.currency = this.user.currency;
                        App.weight_units = this.user.general_weight_units;
                        this.load().then(() => {
                            App.dateFormat = this.user.date_format_js;
                            App.dateFormatShort = this.user.date_format_js_short;
                        });
                    }else if(from=='pedigree'){
                        this.success_pedigree = res;
                    }else if(from=='schedule'){
                        this.success_schedule = res;
                    }else{
                        this.success = res;
                    }
                },
                response => { this.errors = response.errors; }
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


            // wtf
            // //Force to push into vuejs model
            // //I'm sure exist another way to do this, more elegant.
            // if(!this.user.breedchains) this.user.breedchains = { icon: {}, days: {}, name: {}};
            //this.user.breedchains.icon[id] = chain.icon;
            //this.user.breedchains.days[id] = chain.days;
            //this.user.breedchains.name[id] = chain.name;




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

        },

        canRemove(chain) {
            return [
                'fa-venus-mars bg-blue original',
                'fa-birthday-cake bg-green original',
                'fa-balance-scale bg-yellow first-weight'
            ].indexOf(chain.icon) === -1
        },

        load: function() {
            return api.getUserSettingsData(this.user_id).then(data => {
                this.user = data.user;
                this.chains = data.chains;
                this.digest_enabled = this.user.digest_day != -1;

                this.$nextTick(function () {
                    this.loaded = true;
                })
            });
        },

        updateNotificationManagerState() {
            const manager = window.NotificationManager;

            manager.isAvailable().then(available => {
                this.supports_notifications = available;
            });
            manager.isDisabled().then(disabled => {
                this.notifications_disabled = disabled;
            });
            manager.isSubscribed().then(subscribed => {
                this.notifications_subscribed = subscribed;
            });
        },
        disableNotifications() {
            NotificationManager.unsubscribe(() => {
                this.updateNotificationManagerState();
            });
        },
        subscribeToNotifications() {
            NotificationManager.subscribe(() => {
                this.updateNotificationManagerState();
            }, () => {
                this.updateNotificationManagerState();
            })
        },
        sendTestNotification() {
            api.requestTestNotification();
        }
    },
    ready: function () {
        this.load();

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

        this.updateNotificationManagerState();
        setInterval(() => { this.updateNotificationManagerState(); }, 1000);
    }
};
