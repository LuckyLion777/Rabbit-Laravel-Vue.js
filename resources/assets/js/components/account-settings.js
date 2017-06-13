import referrals from '../mixins/referrals.js'
import socials from '../mixins/socials.js'

App.Components.AccountSettings = {
    template: "#account-settings-template",
    data: function () {
        return {
            user: {},
            user_id: "",
            success: {},
            errors: {},
            loaded: false,
            subscription_updated: moment().unix()
        }
    },
    mixins: [App.Mixins.Subscribes, referrals, socials],
    computed: {
        isSubscribed: function() {
            return this.subscription_updated && App.isSubscribed;
        },
        isPremium: function() {
            return this.subscription_updated && App.isPremiumSubscribed;
        }
    },
    methods: {
        updateSettings: function () {
            this.success = {};
            this.errors = {};

            api.postUserSettings(this.user_id, this.user).then(
                data => { this.success = data; },
                response => { this.errors = response.data; }
            );
        }
    },
    ready: function () {
        api.getUserSettingsData(this.user_id).then(data => {
            this.user = data.user;
            this.$nextTick(() => { this.loaded = true; });
        });
    }
};
