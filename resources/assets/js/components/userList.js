App.Components.Users = {
    template: "#users-template",
    data: function () {
        return {
            test: ['asd'],
            users:[],
            usersTotal: 0,
            breedersTotal: 0,
            littersTotal: 0,
            kitsTotal: 0,
            pages: 1,
            usersPremium: 0,
            usersBasic: 0,
            usersMini: 0,
            usersForever: 0,
            filters: {
                id: '',
                email: '',
                name: '',
                stripe_id: '',
                trial_ends_at: {
                    from: null,
                    to: null
                }
            },
            filterRules: {
                id: 'number',
                email: 'string',
                name: 'string',
                stripe_id: 'string',
                trial_ends_at: 'daterange',
                subscription_status: 'string'
            },
            searchFields: ['id', 'email', 'name', 'stripe_id']
        }
    },
    props: [],
    computed: {
        page: function () {
            return this.$route.query.page || 1;
        },
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        }
    },
    watch: {
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        },
        page: function () {
            this.updateList();
        }
    },

    methods: {
        confirmDelete: function (user) {
            $('#delete').modal('show');
            this.toDelete = user;
        },
        deleteUser: function () {
            var user = this.toDelete;
            api.deleteUser(user).then(
                () => { this.users = _.without(this.users, _.findWhere(this.users, {id: user.id})); },
                () => {}
            );
        },

        prevPage: function () {
            if (this.page - 1 > 0) {
                this.$router.go({
                    path: this.currentRoute,
                    query: {
                        page: this.page - 1
                    }
                });
            }
        },

        nextPage: function () {
            if (Number(this.page) + 1 <= this.pages) {
                this.$router.go({
                    path: this.currentRoute,
                    query: {
                        page: Number(this.page) + 1
                    }
                });
            }
        },

        updateList: function () {
            var data = {page: this.page, paginated: 1};

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getUsers(data).then(users => {
                this.users = users.data;
                this.usersTotal = users.total;
                this.pages = users.last_page;

                this.itemsLoaded = users.current_page;
                this.disableLoadMore = false;
                this.totalItems = users.total
            });
        },

        loadMore: function () {

            var data = {page: parseInt(this.itemsLoaded) + 1};

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getUsers(data).then(users => {
                this.mergeByProperty(this.users, users.data, 'id');
                this.usersTotal = users.total;
                this.pages = users.last_page;

                this.itemsLoaded = users.current_page;
                this.disableLoadMore = false;
                this.totalItems = users.total;
                this.itemsLoaded = users.current_page;
                if (users.last_page <= users.current_page) {
                    this.disableLoadMore = true;
                } else {
                    this.disableLoadMore = false;
                }
            });

        }
    },
    ready: function () {
        this.updateList();
        api.getUsersDashboard().then(data => {
            this.usersForever = data.usersForever;
            this.usersPremium = data.usersPremium;
            this.usersBasic = data.usersBasic;
            this.usersMini = data.usersMini;
            this.breedersTotal = data.breedersTotal;
            this.littersTotal = data.littersTotal;
            this.kitsTotal = data.kitsTotal;
        });
    },
    mixins: [App.Mixins.Filterable]
};
