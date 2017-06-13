/**
 * Empty cage card template.
 */
App.emptyCageCardTemplate = {
    id: 0,
    name: '',
    type: '',
    size: '',
    hole: '0',
    fields: {},
    orientation: 'vertical'
};

/**
 * Cage cards templates list component.
 */
App.Components.CageCardsTemplatesList = {
    template: '#cage-cards-templates-list',
    data: function () {
        return {
            template: _.extend({}, App.emptyCageCardTemplate),
            paginatedtemplates: [],
            alltemplates: [],
            deleteCageCardTemplateModal: {},
            copyCageCardTemplateModal: {},
            activeCageCardTemplate: {},
            cageCardTemplateFormModal: {},
            cageCardPrintModal: {},
            page: 1,
            fieldsoptions: [],
            disableLoadMore: true,
            visibleItemsPerPage: 10
        };
    },
    components: {
        'cage-cards-print': App.Components.CageCardsPrint,
        'cage-cards-template-form': App.Components.CageCardsTemplateForm
    },
    computed: {
        confirmTarget: function () {
            return this.activeCageCardTemplate.name;
        },
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        }
    },
    methods: {
        /**
         * Update list of templates action.
         */
        updateList: function () {
            var request = {page: this.page};

            api.getCageCardsTemplates(request).then(data => {
                this.disableLoadMore = data.totalPages < 2;
                this.paginatedtemplates = data.templates;
            });
        },

        /**
         * Load more.
         */
        loadMore: function() {
            var scope = this;

            ++scope.page;

            api.getCageCardsTemplates({page: scope.page}).then(data => {
                scope.paginatedtemplates = scope.paginatedtemplates.concat(data.templates);

                scope.disableLoadMore = scope.page == data.totalPages;
            });
        },

        /**
         * Delete template modal show.
         */
        deleteModal: function (template) {
            this.activeCageCardTemplate = template;
            this.deleteCageCardTemplateModal.modal('show');
        },

        /**
         * Delete action.
         */
        delete: function () {
            this.deleteCageCardTemplateModal.modal('hide');

            api.deleteCageCardTemplate(this.activeCageCardTemplate).then(() => {
                this.updateList();
            });
        },

        /**
         * Copy template action.
         */
        copy: function (template) {
            this.activeCageCardTemplate = template;

            api.copyCageCardTemplate(this.activeCageCardTemplate).then(() => {
                this.updateList();
            });
        },

        /**
         * Add template modal.
         */
        addNewCageCardTemplateModal: function () {
            App.vent.trigger('cageCardTemplate.modal.open');

            this.template = JSON.parse(JSON.stringify(App.emptyCageCardTemplate));

            this.cageCardTemplateFormModal.modal({
                backdrop: 'static',
                keyboard: false
            });
            this.cageCardTemplateFormModal.modal('show');
        },

        /**
         * Edit template modal.
         */
        editCageCardTemplateModal: function(template) {
            App.vent.trigger('cage-card-template.modal.open');

            this.template = _.extend({}, template);

            this.cageCardTemplateFormModal.modal({
                backdrop: 'static',
                keyboard: false
            });
            this.cageCardTemplateFormModal.modal('show');

            api.getCageCardTemplateFieldsOptionsList().then(data => {
                this.fieldsoptions = this.template.type == 'litter' ? data.litter : data.breeder;
            });
        },

        /**
         * Add new template modal form request.
         */
        newCageCardTemplateModalRequest: function(){
            if(this.$route.query.newCageCardTemplateModal){
                this.addNewCageCardTemplateModal();
            }
        },

        /**
         * Print batch request.
         */
        printBatchModalRequest: function(){
            if(this.$route.query.printBatchModal){
                this.printModal();
            }
        },

        /**
         * Open print modal.
         */
        printModal: function (template) {
            this.template = template !== undefined ? _.extend({}, template) : {};

            this.cageCardPrintModal.modal('show');
        },

        /**
         * Add new cage card template modal sidebar link handler.
         */
        addNewModalSidebarLinkHandler: function (template) {
            var scope = this;

            // Add new modal request.
            scope.$watch('$route.query.newCageCardTemplateModal', scope.newCageCardTemplateModalRequest , { deep: true });

            if(scope.$route.query.newCageCardTemplateModal){
                scope.addNewCageCardTemplateModal();
            }

            // Dismiss modal handling.
            $(document).on('hide.bs.modal', '#cage-cards-template-form-modal', function (event){
                if($(event.target).is('#cage-cards-template-form-modal') && scope.$route.query.newCageCardTemplateModal) {
                    scope.$route.router.go('/cage-cards/templates')
                }
            });
        },

        /**
         * Print batch sidebar link handler.
         */
        printBatchSidebarLinkHandler: function (template) {
            var scope = this;

            // Print batch modal request.
            scope.$watch('$route.query.printBatchModal', scope.printBatchModalRequest , { deep: true });

            if(scope.$route.query.printBatchModal){
                scope.printModal();
            }

            // Dismiss modal handling.
            $(document).on('hide.bs.modal', '#cage-cards-print-modal', function (event){
                if($(event.target).is('#cage-cards-print-modal') && scope.$route.query.printBatchModal) {
                    scope.$route.router.go('/cage-cards/templates')
                }
            });
        },
    },
    ready: function () {
        var scope = this;

        // Jquery elements assignment.
        scope.deleteCageCardTemplateModal = $('#delete-cage-cards-template-modal');
        scope.copyCageCardTemplateModal = $('#copy-cage-cards-template-modal');
        scope.cageCardTemplateFormModal = $('#cage-cards-template-form-modal');
        scope.cageCardPrintModal = $('#cage-cards-print-modal');

        // Update list.
        scope.updateList();

        // Sidebar link handlers.
        scope.addNewModalSidebarLinkHandler();
        scope.printBatchSidebarLinkHandler();

        // Get list of all templates.
        if (!this.alltemplates.length) {
            api.getCageCardsTemplates({}).then(data => {
                this.alltemplates = data.templates;
            });
        }
    }
};