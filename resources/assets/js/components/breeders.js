App.emptyBreeder = {
    "id": 0,
    "category_id": '1',
    "name": '',
    "breed": '',
    "cage": '',
    "tattoo": '',
    "sex": 'doe',
    "weight": '',
    "father_id": 0,
    "mother_id": 0,
    "father_name": null,
    "mother_name": null,
    "color": '',
    "date_of_birth": null,
    "aquired": moment(new Date).format(App.dateFormat),
    "registration_number": "",
    "champion_number": "",
    "legs": "",
    "archived": 0,
    "died": 0,
    "died_at": null,
    "butchered": 0,
    "butchered_at": null,
    "sold_at": null,
    "status": "",
    "status_date": null,
    "image": {
        "name": '',
        "path": '',
        "temp": true,
        "oldImage": '',
        "delete": false,
    },
    "notes": ''
};


App.Components.Breeders = {
    template: "#breeders-template",
    data: function () {
        return {
            breeder: _.extend({}, App.emptyBreeder),
            breeders: [],
            transfers: [],
            pages: 1,
            activeBreeder: {},
            plans: [],
            activeBirth: {},
            loading: 1,
            litters: [],
            order: null,
            soldValue: 0,
            transferTo: '',
            transferErrors: {},
            categories: [],

            filters: {
                name: '',
                breed: '',
                cage: '',
                color: '',
                tattoo: '',
                date_of_birth: {
                    from: null,
                    to: null
                },
                aquired: {
                    from: null,
                    to: null
                },
                category_id: '',
                bred: null
            },
            filterRanges: {
                date_of_birth: [],
                aquired: []
            },
            filterRules: {
                name: 'string',
                breed: 'string',
                cage: 'string',
                color: 'string',
                tattoo: 'string',
                date_of_birth: 'daterange',
                aquired: 'daterange',
                category_id: 'number',
                bred: 'bool'
            },
            searchFields: ['name', 'breed', 'cage', 'color', 'tattoo', 'notes'],
            subDropdownOpend: false,
            subEditFunctionsOpened: null,
            bredArr: [
                {
                    name: 'All',
                    value: null
                },
                {
                    name: 'Bred',
                    value: true
                },
                {
                    name: 'Open',
                    value: false
                }
            ],
            openDoes: false,

            /*
            * additional functionality
            * */
            // Cage card template.
            template: {},
            // alltemplates: {},
            // profileid: 0,
            // confirmTarget: 'breeder',
            //
            /*
             * additional functionality end
             * */
        }
    },
    props: [],
    components: {
        'breeder-form': App.Components.BreederForm,
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher,

        // 'kit-form': App.Components.KitForm,
        // 'litter-box': App.Components.LitterBox,
        // 'breeder-butcher': App.Components.BreederButcher,
        // 'litter-form': App.Components.LitterForm,
        // 'task-form' : App.Components.TaskForm,
        // 'breeder-pedigree': App.Components.Pedigree,
        // "ledger-table": App.Components.LedgerTable,
        // 'cage-cards-print': App.Components.CageCardsPrint
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
            return this.activeBreeder.name;
        },
        // savedFilters: {
        //     get: function () {
        //         return JSON.parse(localStorage.getItem('filters'));
        //     },
        //     set: function () {
        //         localStorage.setItem('filters', JSON.stringify(this.filters));
        //     }
        // }
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
                console.log('this.$route: ', this.$route.path);
                // if (this.$route.path.indexOf('opendoes') >= 0) {
                //     this.openDoes = true;
                // } else {
                //     this.openDoes = false;
                // }
            },
            deep: true
        },
        '$route.path' : function () {
            if (this.$route.path.indexOf('opendoes') >= 0) {
                this.openDoes = true;
                this.filters.bred = false;
            } else {
                this.openDoes = false;
                this.filters.bred = null;
            }
        }
    },

    methods: {
        hoverSubdropdown: function (event) {
            var elem = event.target;
            if (elem.classList.contains('hoverClass'))
                this.subDropdownOpend = true;
            else
                this.subDropdownOpend = false;
        },
        saveFilters: function () {
            localStorage.setItem('filters', JSON.stringify(this.filters));
        },
        getFilters: function () {
            return JSON.parse(localStorage.getItem('filters'));
        },
        changeOrder: function(value){
            this.order = value;
        },
        updateList: function () {
            var data = { page: this.page };
            if(this.order){
                data.order = this.order;
            }

            data.archived = this.filter == 'archive' ? 1 : 0;
            data.sold = this.filter == 'sold' ? 1 : 0;
            data.butchered = this.filter == 'butchered' ? 1 : 0;
            data.died = this.filter == 'died' ? 1 : 0;

            switch (this.filter) {
                case 'bucks':
                    data.sex = 'buck';
                    break;
                case 'does':
                    data.sex = 'doe';
                    break;
            }
            data.filters = this.filters;
            data.searchQuery = this.searchQuery;

            api.getTransferBreeders().then(data => {
                this.transfers = data.breeders;
            });

            api.getBreeders(data).then(data => {
                this.loading = 0;
                this.breeders = data.breeders.data;
                this.pages = data.breeders.last_page;
                this.order = data.order;
                this.itemsLoaded = data.breeders.current_page;
                this.disableLoadMore = false;
                this.totalItems = data.breeders.total;
                $('#f-breeder-date_of_birth').trigger('triggerDaterangepicker');
                $('#f-breeder-aquired').trigger('triggerDaterangepicker');
            });

            this.loadCategories();
        },

        acceptTransfer: function (transfer) {
            api.acceptTransfer(transfer).then(data => {
                if (data.result) {
                    this.$router.go(data.result.url.split('/#!').pop());
                }
            });
        },

        addNew: function () {
            api.checkBreedersLimit().then(data => {
                if (data.ok) {
                    App.vent.trigger('breeders.modal.open');
                    this.breeder = JSON.parse(JSON.stringify(App.emptyBreeder));
                    $('#breeder-form').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#breeder-form').modal('show');
                } else {
                    $('#breeder-limit-alert').modal('show');
                }
            });
        },

        editModal: function (breeder) {
            App.vent.trigger('breeders.modal.open');
            this.breeder = _.extend({}, breeder);
            $('#breeder-form').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#breeder-form').modal('show');
        },

        initUploader: function () {
            var self = this;
            $(self.$els['image']).fileupload({
                dataType: 'json',
                paramName: 'image',
                formData: {
                    "_token": App.token,
                    "_method": "POST",
                },
                url: '/admin/images/uploadImage',
                done: function (e, data) {
                    self.breeder.image.name = data.result.image.name;
                    self.breeder.image.path = data.result.image.path;
                    self.breeder.image.temp = data.result.image.temp;
                }
            });
        },

        getGenderClass: function (sex) {
            return sex == "buck" ? "bg-aqua-active" : "bg-maroon-active";
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

        deleteImage: function () {
            this.breeder.image = {name: "", path: "", temp: true, oldImage: "", delete: false};
        },

        archiveModal: function (breeder) {
            api.checkBreedersLimit(true).then(data => {
                if (data.ok) {
                    this.activeBreeder = breeder;
                    $('#archive-breed-modal').modal('show');
                } else {
                    $('#breeder-limit-alert').modal('show');
                }
            });
        },
        archive: function () {
            $('#archive-breed-modal').modal('hide');
            api.archiveBreeder(this.activeBreeder).then(
                () => { $('#id_'+this.activeBreeder.id).slideUp(200); },
                response => {
                    if (response.data === 'breeders-limit-exceeded') {
                        $('#breeder-limit-alert').modal('show');
                    }
                }
            );
        },

        soldModal: function(breeder) {
            api.checkBreedersLimit(true).then(data => {
                if (data.ok) {
                    this.activeBreeder = breeder;
                    $('#sold-breed-modal').modal('show');
                } else {
                    $('#breeder-limit-alert').modal('show');
                }
            });
        },
        sold: function() {
            $('#sold-breed-modal').modal('hide');
            api.sellBreeder(this.activeBreeder, this.soldValue).then(
                () => {
                    this.soldValue = 0;
                    $('#id_' + this.activeBreeder.id).slideUp(200);
                },
                response => {
                    $('#transfer-breed-modal').modal('hide');
                    if (response.data == 'breeders-limit-exceeded') {
                        $('#breeder-limit-alert').modal('show');
                    }
                }
            );
            $('#transfer-breed-modal').modal('show');
        },

        transfer: function() {
            $('#transfer-breed-modal').modal('hide');
            $('#confirm-transfer-breed-modal').modal('show');
        },

        confirmTransfer: function() {
            api.transferBreeder(this.activeBreeder, this.transferTo).then(
                () => {
                    $('#confirm-transfer-breed-modal').modal('hide');
                    this.transferTo = '';
                    this.transferErrors = {};
                },
                response => {
                    this.transferErrors = response.data;
                    this.unconfirmTransfer();
                }
            );
        },
        unconfirmTransfer: function() {
            $('#confirm-transfer-breed-modal').modal('hide');
            $('#transfer-breed-modal').modal('show');
        },

        declineTransferModal: function (transfer) {
            this.activeBreeder = transfer;
            $('#decline-transfer-breed-modal').modal('show');
        },
        declineTransfer: function() {
            $('#decline-transfer-breed-modal').modal('hide');
            api.declineTransfer(this.activeBreeder).then(() => {
                $('#transfer_id_' + this.activeBreeder.transfer_id).slideUp(200);
            });
        },

        unsoldModal: function(breeder) {
            api.checkBreedersLimit().then(data => {
                if (data.ok) {
                    this.activeBreeder = breeder;
                    $('#unsold-breed-modal').modal('show');
                } else {
                    $('#breeder-limit-alert').modal('show');
                }
            });
        },
        unsold: function() {
            $('#unsold-breed-modal').modal('hide');
            api.unsellBreeder(this.activeBreeder).then(
                () => { $('#id_' + this.activeBreeder.id).slideUp(200); },
                response => {
                    if (response.data === 'breeders-limit-exceeded') {
                        $('#breeder-limit-alert').modal('show');
                    }
                }
            );
        },

        unarchiveModal: function (breeder) {
            api.checkBreedersLimit().then(data => {
                if (data.ok) {
                    this.activeBreeder = breeder;
                    $('#unarchive-breed-modal').modal('show');
                } else {
                    $('#breeder-limit-alert').modal('show');
                }
            });
        },
        unarchive: function () {
            $('#unarchive-breed-modal').modal('hide');
            api.unarchiveBreeder(this.activeBreeder).then(
                () => { $('#id_'+this.activeBreeder.id).slideUp(200); },
                response => {
                    if (response.data === 'breeders-limit-exceeded') {
                        $('#breeder-limit-alert').modal('show');
                    }
                }
            );
        },

        deleteModal: function (breeder) {
            this.activeBreeder = breeder;
            $('#delete-breed-modal').modal('show');
        },
        delete: function () {
            $('#delete-breed-modal').modal('hide');
            api.deleteBreeder(this.activeBreeder).then(() => {
                $('#id_'+this.activeBreeder.id).slideUp(200);
            });
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
        loadLitters: function () {
            var data = { page: this.page };
            data.archived = this.filter == "archive" ? 1 : 0;

            api.getLitters(data).then(res => {
                this.loading = 0;
                this.litters = res.data;
            });
        },

        handleChange: function(){
            if(this.$route.query.newModal){
                this.addNew();
            }
        },
        loadMore: function(){

            var data = { page: parseInt(this.itemsLoaded) + 1 };
            if(this.order){
                data.order = this.order;
            }

            data.archived = this.filter == 'archive' ? 1 : 0;
            data.sold = this.filter == 'sold' ? 1 : 0;
            data.butchered = this.filter == 'butchered' ? 1 : 0;
            data.died = this.filter == 'died' ? 1 : 0;
            switch (this.filter) {
                case 'bucks':
                    data.sex = 'buck';
                    break;
                case 'does':
                    data.sex = 'doe';
                    break;
            }

            data.filters = this.filters;
            data.searchQuery = this.searchQuery;


            this.$http.get('/admin/breeders', data, function (data) {
                this.loading = 0;
                //this.breedersLoaded += data.breeders.data.length;
                //this.breeders.push.apply(this.breeders, data.breeders.data);
                this.mergeByProperty(this.breeders, data.breeders.data, 'id');
                this.pages = data.breeders.last_page;
                this.order = data.order;
                this.itemsLoaded = data.breeders.current_page;
                this.totalItems = data.breeders.total;
                if(data.breeders.last_page <= data.breeders.current_page){
                    this.disableLoadMore = true;
                } else {
                    this.disableLoadMore = false;
                }
                console.log('*******************');
                console.log('data from get breeders: ', this.breeders);
                console.log('empty breeder: ', this.breeder);
                console.log('*******************');
            });
        },
        loadCategories: function () {
            api.getBreederCategories().then(data => {
                this.categories = data.categories;
            });
        },

        showAge: function(date_of_birth){
            if (!date_of_birth) {
                return '-'
            }
            const years = App.roundTo(moment().diff(App.parseMoment(date_of_birth), 'years', true), 2);
            if(years < 1){
                const months = App.roundTo(moment().diff(App.parseMoment(date_of_birth), 'months', true), 2);
                if (months === 1){
                    return '1 mth'
                } else {
                    return months  + ' mths';
                }
            }
            return years === 1? '1 yr': years + ' yrs';
        },


        /**
         * Cage card.
         */
        // cageCard: function(id){
        //     this.profileid = id;
        //
        //
        //     var request = {type: 'breeder'};
        //     this.template = {};
        //
        //     api.getCageCardsTemplates(request).then(data => {
        //         this.alltemplates = data.templates;
        //     });
        //
        //     this.profileid = this.breeder.id;
        //
        //     $('#cage-cards-print-modal').modal('show');
        //
        // },
        /**
         * butcher unbutcher
         *
         */
        // butcherBreederModal: function () {
        //     $('#breeder-butcher-modal').modal('show');
        // },
        //
        // undoButcher: function(){
        //
        //     var data = {
        //         breeders: [this.breeder],
        //         butchered: 0
        //     };
        //
        //     api.butcherBreeder(this.breeder, data).then(() => {
        //         this.breeder.butchered = 0;
        //     });
        // },
        /**
         *
         * died undo died
         *
         */
        // showDied: function(){
        //     $('#died-modal-breeder').modal('show');
        // },
        //
        // died: function() {
        //     var data = {
        //         died: 1
        //     };
        //     api.diedBreeder(this.breeder, data).then(() => {
        //         $('#died-modal-breeder').modal('hide');
        //         this.breeder.died = true;
        //         this.$dispatch('breederDied', this.breeder.id);
        //     });
        // },
        // undoDied: function(){
        //
        // }

        openBreeder: function (item, id) {
            localStorage.setItem('action', item);
            this.$route.router.go('/profile/' + id);
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
        var cat_id = localStorage.getItem('category');
        console.log('cat id: ',cat_id);
        if (cat_id) {
            this.filters.category_id = cat_id;
            localStorage.removeItem('category');
            console.log('category id: ', this.filters.category_id);
        }

        if (this.$route.path.indexOf('opendoes') >= 0) {
            this.openDoes = true;
            this.filters.bred = false;
        } else {
            this.openDoes = false;
            this.filters.bred = null;
        }

        this.initUploader();
        this.updateList();
        App.vent.on('breeders.new', this.addNew.bind(this));
        App.vent.on('breederSaved', () => {
            this.loadCategories();
        });
        this.$watch('$route.query.newModal', this.handleChange , { deep: true });
        var self = this;
        if(this.$route.query.newModal){
            this.addNew();
        }
        $(document).on('hide.bs.modal', '#breeder-form', function(event){
            if($(event.target).is('#breeder-form') && self.$route.query.newModal) {
                self.$route.router.go('breeders')
            }
        });
        App.MobileTypes();
        this.birthModal();
        this.loadLitters();
    },
    mixins: [App.Mixins.Butcherable, App.Mixins.Filterable]
    // mixins: [App.Mixins.Butcherable, App.Mixins.Filterable, App.Mixins.DeathReason]
};
