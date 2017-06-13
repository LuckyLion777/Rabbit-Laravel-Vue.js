App.emptyLitter = {
    "id": 0,
    "given_id": '',
    "kits_amount": '',
    "kits":[],
    "father_id": 0,
    "mother_id": 0,
    "bred": moment(new Date).format(App.dateFormat),
    "born": moment(new Date).format(App.dateFormat),
    "notes": ''
};


App.Components.Litters = {
    template: "#litters-template",
    data: function () {
        return {
            litter: _.extend({}, App.emptyLitter),
            litters: [],
            pages: 1,
            activeLitter: {},
            activeKits: [],
            loading: 1,
            plans: [],
            activeBirth: {},
            order: null,
            bucks: [],
            does: [],
            filters: {
                given_id: '',
                buck: '',
                doe: '',
                born: {
                    from: null,
                    to: null
                },
                bred: {
                    from: null,
                    to: null
                }
            },
            filterRanges: {
                born: [],
                bred: []
            },
            filterRules: {
                given_id: 'string',
                buck: {
                    type: 'relation',
                    relationName : 'parents',
                    relationFilterField: 'id',
                    relationField: 'sex',
                    valueType: 'number'
                },
                doe: {
                    type: 'relation',
                    relationName : 'parents',
                    relationFilterField: 'id',
                    relationField: 'sex',
                    valueType: 'number'
                },
                born: 'daterange',
                bred: 'daterange'
            },
            searchFields: ['given_id', 'notes']
        }
    },
    props: [],
    components: {
        'litter-form': App.Components.LitterForm,
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher
    },
    computed: {
        filter: function () {
            return this.$route.params.action;
        },
        page: function () {
            return this.$route.query.page || 1;
        },
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        },
        confirmTarget: function () {
            return "this litter";
        }
    },

    watch: {
        filter: function () {
            this.updateList();
        },
        page: function () {
            this.updateList();
        },
        order: function () {
            this.updateList();
        },
        filters: {
            handler: function (newVal, oldVal) {
                console.log('*************');
                console.log('Filters Saved');
                this.saveFilters();
                console.log(this.getFilters());
                console.log('*************');
            },
            deep: true
        }
    },

    events: {
        'litter-archived' : function(msg){
            $('#id_' + msg.litter).slideUp(200);
        }
    },

    methods: {
        saveFilters: function () {
            localStorage.setItem('filters2', JSON.stringify(this.filters));
        },
        getFilters: function () {
            return JSON.parse(localStorage.getItem('filters2'));
        },
        changeOrder: function(value){
            this.order = value;
        },
        age: function (litter) {
            var date = litter.born;
            var born = App.parseMoment(date);

            var now = moment().startOf('day');
            var age  = now.diff(born, "days");
            if(litter.butchered_at) {
                var butchered = moment(litter.butchered_at, 'YYYY-MM-DD H:i:s').startOf('day');
                age  = butchered.diff(born, "days");
            }

            var modulo = age % 7;
            var weeks = 0;
            if(modulo === 0) {
                weeks = age / 7;
            } else {
                weeks = (age / 7).toFixed(1);
            }

            if (age < 1) {
                return "Today";
            }
            if (age < 7) {
                return age + " days";
            } else {
                return  weeks + " wks";
            }
        },
        updateList: function () {
            var data = { page: this.page};
            if(this.order){
                data.order = this.order;
            }
            data.archived = this.filter == "archive" ? 1 : 0;
            data.butchered = this.filter == "butchered" ? 1 : 0;

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getLitters(data).then(data => {
                this.loading = 0;
                this.litters = data.litters.data;
                this.pages = data.litters.last_page;
                this.order = data.order;

                this.itemsLoaded = data.litters.current_page;
                this.disableLoadMore = false;
                this.totalItems = data.litters.total;

                $('#f-litter-born').trigger('triggerDaterangepicker');
                $('#f-litter-breed').trigger('triggerDaterangepicker');
            });
        },

        newLitter: function () {
            this.litter = _.extend({}, App.emptyLitter);
            $('#litter-form').modal('show');
        },

        getGenderClass: function (sex) {
            return sex == "male" ? "bg-aqua-active" : "bg-maroon-active";
        },

        onlyNumbers: function (e) {
            return e.charCode >= 48 && e.charCode <= 57;
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


        editModal: function (litter) {
            this.litter = litter;
            $('#litter-form').modal('show');
        },

        loadKits: function () {
            api.getLitterKitsWeighOfType(this.activeLitter.id, 'rabbitkit').then(kits => {
                this.activeKits = kits;
            });
        },

        weightModal: function (litter) {
            this.activeLitter = litter;
            //this.loadKits();
            $('#litter-weight-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#litter-weight-modal').modal('show');
        },

        butcherModal: function (litter) {
            this.activeLitter = litter;
            //this.loadKits();
            $('#litter-butcher-modal').modal('show');
        },

        archiveModal: function (litter) {
            this.activelitter = litter;
            $('#archive-breed-modal').modal('show');
        },
        archive: function () {
            $('#archive-breed-modal').modal('hide');
            api.archiveLitter(this.activelitter).then(() => {
                $('#id_' + this.activelitter.id).slideUp(200);
            });
        },

        unarchiveModal: function (litter) {
            this.activelitter = litter;
            $('#unarchive-breed-modal').modal('show');
        },
        unarchive: function () {
            $('#unarchive-breed-modal').modal('hide');
            api.unarchiveLitter(this.activelitter).then(() => {
                $('#id_' + this.activelitter.id).slideUp(200);
            });
        },

        deleteModal: function (litter) {
            this.activelitter = litter;
            $('#delete-breed-modal').modal('show');
        },
        delete: function () {
            $('#delete-breed-modal').modal('hide');
            api.deleteLitter(this.activelitter).then(() => {
                $('#id_' + this.activelitter.id).slideUp(200);
            });
        },

        calcKits: function (litter) {
            return litter.kits_died ? litter.kits_amount - litter.kits_died : litter.kits_amount;
        },


        father: function (parents) {
            return _.find(parents, function (item) { return item.sex == "buck" });
        },
        mother: function (parents) {
            return _.find(parents, function (item) { return item.sex == "doe" });
        },
        birthModal: function () {
            api.getUsersPlans().then(plans => {
                this.plans = plans;
            });
            this.activeBirth = {breedplan: '-1', born: moment(new Date()).format(App.dateFormat)};
        },
        recordBirth: function () {
            api.recordBirth(this.activeBirth).then(() => {
                this.activeBirth = {};
                $('#birth').modal('hide');
            });
        },

        handleChange: function(){
            if(this.$route.query.newModal){
                this.newLitter();
            }
        },

        loadMore: function(){

            var data = { page: parseInt(this.itemsLoaded) + 1 };
            if(this.order){
                data.order = this.order;
            }

            data.archived = this.filter == "archive" ? 1 : 0;
            data.butchered = this.filter == "butchered" ? 1 : 0;

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            this.$http.get('/admin/litters', data, function (data) {
                this.loading = 0;
                this.mergeByProperty(this.litters, data.litters.data, 'id');
                this.pages = data.litters.last_page;
                this.order = data.order;

                this.itemsLoaded = data.litters.current_page;
                this.disableLoadMore = false;
                this.totalItems = data.litters.total;

                this.itemsLoaded = data.litters.current_page;
                this.totalItems = data.litters.total;
                if(data.litters.last_page <= data.litters.current_page){
                    this.disableLoadMore = true;
                } else {
                    this.disableLoadMore = false;
                }
            });
        },

        getParents: function () {
            this.$http.get('/admin/breeders/getList', function (breeders) {
                this.bucks = breeders.bucks;
                this.does = breeders.does;
            });
        },

        openLitter: function (item, id) {
            localStorage.setItem('action', item);
            this.$route.router.go('/litterprofile/' + id);
        }

    },
    ready: function () {

        console.log('*************');
        console.log('Ready Component');
        console.log(this.filters);
        console.log(this.getFilters());
        if (this.getFilters())
            this.filters = this.getFilters();
        console.log('*************');


        this.updateList();
        App.MobileTypes();
        this.getParents();
        App.vent.on('litters.new', this.newLitter.bind(this));
        this.$watch('$route.query.newModal', this.handleChange , { deep: true });
        var self = this;
        if(this.$route.query.newModal){
            this.newLitter();
        }
        $(document).on('hide.bs.modal', '#litter-form', function(event){
            if($(event.target).is('#litter-form') && self.$route.query.newModal) {
                self.$route.router.go('litters')
            }
        });
        this.$on('weighted', this.updateList.bind(this));this.birthModal();
        this.birthModal();
    },
    mixins: [App.Mixins.Butcherable, App.Mixins.Filterable]
};
