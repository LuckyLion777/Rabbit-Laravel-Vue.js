'use strict';

export default {
    data() {
        return {
            refs: [],
            referral: '',
            referralErrors: {},

            referred_by: '',
            referrer: '',
            referrerErrors: {},
            referralSent: false,

            referralsCredit: 0
        };
    },
    methods: {
        addReferral() {
            this.referralSent = false;
            api.addReferral(this.referral).then(
                () => {
                    this.referralSent = true;
                    this.referralErrors = {};
                    this.referral = '';
                    this.loadReferralsData();
                },
                response => {
                    this.referralErrors = response.data;
                }
            );
        },
        setReferrer() {
            api.validateReferrer(this.referrer).then(
                () => {
                    this.referrerErrors = {};
                    $('#referred-by-modal').modal('show');
                },
                response => {
                    this.referrerErrors = response.data;
                }
            )
        },
        cancelReferrer() {
            $('#referred-by-modal').modal('hide');
            this.referrer = '';
        },
        saveReferrer() {
            $('#referred-by-modal').modal('hide');
            api.updateReferrer(this.referrer).then(
                () => {
                    this.loadReferralsData();
                    this.referrerErrors = {};
                    this.referrer = '';
                },
                response => {
                    this.referrerErrors = response.data;
                }
            );
        },
        loadReferralsData() {
            api.getUserReferrals().then(data => {
                this.refs = data.referrals;
                this.referred_by = data.referred_by;
                this.referralsCredit = data.referrals_credit;
            });
        },
        clearReferralSent() {
            this.referralSent = false;
        }
    },
    created() {
        if (this.$route.query.referrer) {
            const referrer = this.$route.query.referrer;
            this.$router.go({ path: '/account' });
            this.referrer =  referrer;
            this.setReferrer();
        }
        this.loadReferralsData();
    }
}
