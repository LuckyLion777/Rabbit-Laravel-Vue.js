App.Components.LedgerTable = App.Components.Exploitable.Section.extend({
    template: "#ledger-table-template",
    data: function() {
        return {
            ledger: [],
            daterange: [],
            total: 0,
            loading: true,
            modalUnique: 'ledger',
            showSubscribe: false,
            categories: [],
            filters: {
                name: '',
                description: '',
                category: ''
            },
            filterRules: {
                name: 'string',
                description: 'string',
                category: {
                    type: 'relation',
                    relationName : 'category',
                    relationFilterField: 'id',
                    valueType: 'number'
                }
            },
            searchFields: ['name', 'amount', 'description'],
            
            deleter: api.deleteLedgerEntry,
            archiver: api.archiveLedgerEntry,
            unarchiver: api.unarchiveLedgerEntry
        };
    },
    props: {
        canLoading: {
            default: true
        },
        associated_type: {
            default: null
        },
        associated_id: {
            default: null
        },
        filter: {
            default: ""
        },
        page: {
            default: 1
        },
        hold: {
            default: false
        },
        order: {
            default: function() {
                return "";
            }
        }
    },
    components: {
        'ledger-form': App.Components.LedgerForm
    },
    computed: {
        showLoading: function() {
            return this.canLoading && this.loading;
        },
        dateFrom: function() {
            if (!this.daterange || !this.daterange.length) {
                return null;
            }
            return this.daterange[0].format(App.dateFormat);
        },
        dateTo: function() {
            if (!this.daterange || !this.daterange.length) {
                return null;
            }
            return this.daterange[1].format(App.dateFormat);
        },
        models: function() {
            return this.ledger;
        }
    },
    watch: {
        filter: function () {
            this.updateList();
        },
        page: function () {
            this.updateList();
        },
        daterange: function() {
            this.updateList();
        },
        order: function() {
            this.updateList();
        }
    },
    events: {
        formAdded: function(model) {
            this.updateList();
            return true;
        },
        formUpdated: function(model) {
            this.updateList();
            return true;
        },
        deletedModel: function(model) {
            this.updateList();
            return true;
        },
        archivedModel: function(model) {
            this.updateList();
            return true;
        },
        unarchivedModel: function(model) {
            this.updateList();
            return true;
        },
        load_ledger: function() {
            this.updateList();
        }
    },
    methods: {
        updateList: function () {
            this.$dispatch('updating');

            var data = { page: this.page };

            if (this.filter == "archive") {
                data.archived = 1;
            } else if (this.filter) {
                data.debit = this.filter === "income" ? 1 : 0;
            }

            if (this.associated_type) {
                data.associated_type = this.associated_type;
                data.associated_id = this.associated_id;
            }

            if (this.order) {
                data.order = this.order;
            }

            if (this.dateFrom) {
                data.from = this.dateFrom;
            }
            if (this.dateTo) {
                data.to = this.dateTo;
            }


            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getLedgerEntries(data).then(
                data => {
                    this.ledger = data.ledger.data;
                    this.order = data.order;
                    this.total = data.total;
                    this.loading = false;


                    this.itemsLoaded = data.ledger.current_page;
                    this.disableLoadMore = false;
                    this.totalItems = data.ledger.total;

                    this.$dispatch('updated', data);
                    this.$nextTick(() => {
                        $('#reservation').trigger('triggerDaterangepicker');
                    });

                },
                response => {
                    this.loading = false;
                    if (response.status == 403) {
                        this.showSubscribe = true;
                    }
                }
            );
        },

        loadMore: function () {
            this.$dispatch('updating');

            var data = {  page: parseInt(this.itemsLoaded) + 1  };

            if (this.filter == "archive") {
                data.archived = 1;
            } else if (this.filter) {
                data.debit = this.filter === "income" ? 1 : 0;
            }

            if (this.associated_type) {
                data.associated_type = this.associated_type;
                data.associated_id = this.associated_id;
            }

            if (this.order) {
                data.order = this.order;
            }

            if (this.dateFrom) {
                data.from = this.dateFrom;
            }
            if (this.dateTo) {
                data.to = this.dateTo;
            }

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getLedgerEntries(data).then(
                data => {
                    this.mergeByProperty(this.ledger, data.ledger.data, 'id');
                    this.order = data.order;
                    this.total = data.total;
                    this.loading = false;


                    this.itemsLoaded = data.ledger.current_page;
                    this.disableLoadMore = false;
                    this.totalItems = data.ledger.total;

                    if (data.ledger.last_page <= data.ledger.current_page){
                        this.disableLoadMore = true;
                    } else {
                        this.disableLoadMore = false;
                    }

                    this.$dispatch('updated', data);
                    this.$nextTick(() => {
                        $('#reservation').trigger('triggerDaterangepicker');
                    });

                },
                response => {
                    this.loading = false;
                    if (response.status == 403) {
                        this.showSubscribe = true;
                    }
                }
            );
        },
        loadCategories: function () {
            api.getLedgerCategories().then(data => {
                this.categories = data.categories;
            });
        }
    },
    ready: function () {
        if (!this.hold) {
            this.updateList();
            this.loadCategories();
        }
    },
    mixins: [App.Mixins.Filterable]
});
