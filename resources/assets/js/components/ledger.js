App.emptyLedger = {
	"id": 0,
	"debit": 0,
	"date": '',
    "name": '',
    "amount": 0,
    "description": "",
    "category_id": 1,
    "associated_id": ''
};


App.Components.Ledger = {
    template: "#ledger-template",
    data: function() {
        return {
            pages: 1,
            statistics: {},
            order: this.$route.query.order || "",
            loading: true
        };
    },
    components: {
        'ledger-table': App.Components.LedgerTable
    },
    computed: {
        filter: function() {
            return this.$route.params.filter;
        },
        page: function () {
            return this.$route.query.page || 1;
        },
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        }
    },
    methods: {
        paginize: function(page) {
            var query = {};
            if (page != 1) {
                query.page = page;
            }
            if (this.order) {
                query.order = this.order;
            }

            return query;
        }
    },
    events: {
        updating: function() {
            var data = {};

            if (this.filter == "archive") {
                data.archived = 1;
            } else if (this.filter) {
                data.debit = this.filter === "income" ? 1 : 0;
            }

            api.getLedgerStatistics(data).then(data => {
                this.statistics = data;
            });
        },
        updated: function(data) {
            this.loading = false;
            this.pages = data.ledger.last_page || 1;
            this.$nextTick(function() {
                if (this.$route.query.new) {
                    this.$refs.table.addModel();
                }
            });
        }
    },
    ready: function () {
        this.$watch('$route.query.new', function(val) {
            if (val) {
                this.$refs.table.addModel();
            }
        } );
        $(document).on('hide.bs.modal', '#ledger-form', function(e) {
            setTimeout(function() {
                if (!$(e.target).is(':visible') && this.$route.query.new) {
                    this.$route.router.go('/ledger')
                }
            }.bind(this), 20);
        }.bind(this));
    },
    mixins: [App.Mixins.Currency]
};