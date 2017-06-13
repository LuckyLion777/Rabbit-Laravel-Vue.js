if (!App.Mixins) {
    App.Mixins = {};
}

App.Mixins.Subscribes = {
    data: function() {
        return {
            plan_id: null,
            currentPlan: {},
            plans: [],
            card: this.defaultCard(),
            cardErrors: {},
            subscribing: false,
            discountCode: '',
            subscribingShowLoading: true,
            invoices: [],
            confirm: {},
            token: null
        };
    },
    computed: {
        plan: function() {
            return this.plans.find(plan => plan.id == this.plan_id);
        },
        subscriptionChanged: function() {
            return this.plan_id != this.currentPlan.id;
        },
        confirmSum: function() {
            return this.confirm.lines
                ? Math.max(this.confirm.lines.reduce(function(sum, line) {
                    return line.amount + sum;
                }, 0), 0) / 100
                : this.confirm.amount + this.confirm.setup_price - this.confirm.discount;
        }
    },
    filters: {
        date_format: function(date) {
            return moment.unix(date).format(App.dateFormat);
        }
    },
    methods: {
        defaultCard: function() {
            return {
                attached: false,
                exp_month: '',
                exp_year: moment().format('YY')
            };
        },
        changeCard: function() {
            this.card = this.defaultCard();
        },
        updatePlan: function() {
            this.subscribing = true;
            api.subscribe({ plan_id: this.plan_id, code: this.discountCode }).then(
                data => {
                    this.loadInvoices();
                    this.subscribing = false;
                    this.card = Object.assign(this.defaultCard(), data.card);
                    this.currentPlan = data.plan;
                    this.plan_id = data.plan.id || 'premium_yr';
                    App.isSubscribed = true;
                    App.isPremiumSubscribed = data.plan.on_trial || data.plan.level === 'premium';
                    this.subscription_updated = moment().unix();
                },
                response => {
                    this.loadInvoices();
                    this.subscribing = false;
                    this.cardErrors = response.data;
                    if (!response.status) {
                        // Internet error
                        alert('Your internet connection is not working. Please try again later.');
                    }
                }
            );
        },
        unsubscribe: function() {
            this.subscribing = true;
            api.unsubscribe().then(
                data => {
                    this.subscribing = false;
                    this.currentPlan = data.plan;
                    App.isSubscribed = data.plan.on_trial;
                    App.isPremiumSubscribed = data.plan.on_trial || data.plan.level === 'premium';
                    this.subscription_updated = moment().unix();
                },
                response => {
                    this.subscribing = false;
                    this.cardErrors = response.data;
                    if (!response.status) {
                        // Internet error
                        alert('Your internet connection is not working. Please try again later.');
                    }
                }
            );
        },
        loadInvoices: function () {
            api.getSubscriptionInvoices().then(invoices => {
                this.invoices = invoices;
            });
        },
        loadPlans: function() {
            api.getSubscriptionPlans().then(plans => {
                this.plans = plans;
            });
        },
        loadCardData: function() {
            api.getSubscription().then(data => {
                this.discountCode = data.coupon;
                this.subscribingShowLoading = false;
                if (data.card) {
                    this.card = Object.assign(this.defaultCard(), data.card);
                } else {
                    this.card = this.defaultCard();
                }
                this.currentPlan = data.plan;
                this.plan_id = data.plan.id || 'premium_yr';
            });
        },
        doSubscribe: function() {
            if (this.subscribing) return;
            this.subscribing = true;
            $('#confirm-subscription-modal').modal('hide');
            api.subscribe({ token: this.token, plan_id: this.plan_id, code: this.discountCode }).then(
                data => {
                    $('#callout-trial').fadeOut();
                    this.subscribing = false;
                    this.loadInvoices();
                    this.card = Object.assign(this.defaultCard(), data.card);
                    this.currentPlan = data.plan;
                    this.plan_id = data.plan.id || 'premium_yr';
                    App.isSubscribed = true;
                    App.isPremiumSubscribed = data.plan.on_trial || data.plan.level === 'premium';
                    this.subscription_updated = moment().unix();
                },
                response => {
                    this.loadInvoices();
                    this.subscribing = false;
                    this.cardErrors = response.data;
                    if (!response.status) {
                        // Internet error
                        alert('Your internet connection is not working. Please try again later.');
                    }
                }
            );
        },
        subscribeWithPreview: function() {
            api.previewSubscription({ plan_id: this.plan_id, code: this.discountCode }).then(
                data => {
                    this.confirm = data;
                    this.subscribing = false;
                    $('#confirm-subscription-modal').modal('show');
                },
                response => {
                    this.subscribing = false;
                    this.cardErrors = response.data;
                    if (!response.status) {
                        // Internet error
                        alert('Your internet connection is not working. Please try again later.');
                    }
                }
            );
        },
        processSubscriptionToken: function(preview, code, result) {
            switch (code) {
                case 200:
                    this.token = result.id;
                    if (preview) {
                        this.subscribeWithPreview();
                    } else {
                        this.subscribing = false; // @XXX must have
                        this.doSubscribe();
                    }
                    return;
                case 402:
                    var param;
                    switch (result.error.param) {
                        case 'exp_month':
                        case 'exp_year':
                            param = 'expiration';
                            break;
                        default:
                            param = result.error.param;
                    }
                    this.subscribing = false;
                    this.$set('cardErrors.' + param, [result.error.message]);
                    return;
                default:
                    this.subscribing = false;
                    this.cardErrors.number = ['Unknown error'];
            }
        },
        validate: function() {
            this.cardErrors = {};
            if (!this.plan_id) {
                this.cardErrors.plan_id = ['Select your plan'];
            }
            if (!this.card.number) {
                this.cardErrors.number = ['Card number is not specified'];
            }
            if (!this.card.exp_month || !this.card.exp_year) {
                this.cardErrors.expiration = ['Specify month and year'];
            }
            return _.isEmpty(this.cardErrors);
        },
        subscribe: function() {
            if (this.subscribing || !this.validate()) return;
            if (this.card.cvc === '') delete this.card.cvc;
            this.subscribing = true;
            Stripe.card.createToken(
                _.pick(this.card, ['number', 'cvc', 'exp_month', 'exp_year']),
                this.processSubscriptionToken.bind(this, true)
            );
        }
    },
    ready: function() {
        this.loadPlans();
        this.loadCardData();
        this.loadInvoices();
    }
};
