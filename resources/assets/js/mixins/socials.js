'use strict';

export default {
    data() {
        return {
            socials: [],
            socialError: ''
        };
    },
    methods: {
        disconnectSocial(social_id) {
            api.disconnectSocial(social_id).then(
                () => {
                    this.loadSocials();
                    this.socialError = '';
                },
                response => {
                    this.socialError = response.data.error;
                }
            );
        },
        loadSocials() {
            api.getUserSocialAccounts().then(accounts => {
                this.socials = accounts;
            });
        }
    },
    created() {
        this.loadSocials();
    }
}
