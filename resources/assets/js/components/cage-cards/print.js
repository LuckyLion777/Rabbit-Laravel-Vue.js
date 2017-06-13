/**
 * Cage cards print component.
 */
App.Components.CageCardsPrint = {
    template: '#cage-cards-print-template',
    data: function () {
        return {
            breeders: {},
            litters: {},
            breedersFilter: 'all',
            selectedBreeders: [],
            selectedLitters: [],
            breederCategories: {},
            breedersFilterCategory: 0,
            errors: {}
        };
    },
    props: ['template', 'alltemplates', 'profileid'],
    computed: {},
    methods: {
        /**
         * Select all print models.
         */
        selectAllPrintModels: function (type) {
            var scope = this,
                printArrName = type == 'breeder' ? 'selectedBreeders' : 'selectedLitters',
                allModels = type == 'breeder' ? scope.breeders : scope.litters;

            if ($('#print-' + type + '-all').is(':checked')) {
                $.each(allModels, function (index, value) {
                    if ($.inArray(value.id, scope[printArrName]) == -1) {
                        scope[printArrName].push(value.id);
                    }
                });
            } else {
                scope[printArrName] = [];
            }
        },

        /**
         * Select print model.
         */
        selectPrintModel: function (type, id) {
            var printArrName = type == 'breeder' ? 'selectedBreeders' : 'selectedLitters';

            if ($('#print-' + type + '-' + id).is(':checked')) {
                this[printArrName].push(id);
            } else if ($.inArray(id, this[printArrName]) > -1) {
                var index = this[printArrName].indexOf(id);
                this[printArrName].splice(index, 1);
            }
        },

        /**
         * Get litter's parents joined name.
         *
         * @param litter
         * @returns {string}
         */
        getLitterParentsName: function (litter) {
            var father = litter.father,
                mother = litter.mother,
                name = '';

            if (father && mother) {
                name = this.trimStrTo(father.name, 5) + ' + ' + this.trimStrTo(mother.name, 5);
            }

            return name;
        },

        /**
         * Get list of breeders.
         */
        getListOfBreeders: function () {
            var request = {
                filter: this.breedersFilter,
                category: this.breedersFilterCategory
            };

            api.getCageCardBreedersList(request).then(data => {
                this.breeders = data;
            });

            // Reset selected models.
            this.resetSelectedModels();
        },

        /**
         * Get list of breeders categories.
         */
        getBreedersCategories: function () {
            api.getBreederCategories().then(data => {
                this.breederCategories = data.categories;
            });

        },

        /**
         * Get list of litters.
         */
        getListOfLitters: function () {
            api.getCageCardLittersList().then(data => {
                this.litters = data;
            });

            // Reset selected models.
            this.resetSelectedModels();
        },

        /**
         * Change template.
         */
        changeTemplate: function (e) {
            var scope = this;

            $.each(this.alltemplates, function (index, value) {
                if (value.id == e.target.value) {
                    scope.template = value;
                }
            });

            // Reset selected models.
             this.resetSelectedModels();
        },

        /**
         * Choose breeder category.
         */
        chooseBreedersCategory: function (e) {
            this.breedersFilterCategory = e.target.value;
            this.getListOfBreeders();
        },

        /**
         * Close modal handler.
         */
        closeModalHandler: function() {
            App.vent.trigger('closeModal');
        },

        /**
         * Reset selected models.
         */
        resetSelectedModels: function() {
            this.selectedBreeders = this.selectedLitters = [];
        },

        /**
         * Print batch.
         */
        printBatch: function() {
            var request = {
                template: {
                    type: this.template.type,
                    size: this.template.size,
                },
                modelIds: this.template.type == 'breeder' ? this.selectedBreeders : this.selectedLitters
            };

            // Clear erros before new request.
            this.errors = {};

            api.printBatch(request).then(
                data => {
                    this.$router.go({
                        path: '/cage-cards/stream/1_breeder_4_7_large_2017_05_23_16_57_35.pdf',
                    });
                },
                response => {
                    this.errors = response.data;
                }
            );
        },

        /**
         * Listeners.
         */
        listeners: function() {
            // Close modal event handler.
            App.vent.on('closeModal', () => {
                // Reset state.
                this.resetSelectedModels();
                this.breedersFilter = 'all';
                this.breedersFilterCategory = 0;
            });
        },

        /**
         * Get selected models ids str.
         */
        getSelectedModelsIdsStr: function() {
            var ids = this.template.type == 'breeder' ? this.selectedLitters.join(',') : this.selectedBreeders.join(',');

            if (this.profileid !== undefined && this.profileid != 0) {
                ids = this.profileid;
            }

            return ids
        },

        /**
         * Is print btn visible.
         */
        isPrintBtnVisible: function() {
            var selectedIds = this.getSelectedModelsIdsStr();

            return selectedIds && this.template.id;
        },

        /**
         * Trim str to.
         *
         * @param str
         * @param length
         */
        trimStrTo: function(str, length) {
            var trimmed = str.substr(0, length);

            if (str.length > length) {
                trimmed += '...';
            }

            return trimmed;
        }
    },
    ready: function () {
        // Listeners for triggers.
        this.listeners();

        // Get list of breeders for print page.
        this.getListOfBreeders();

        // Get list of breeders categories.
        this.getBreedersCategories();

        // Get list of litters for print page.
        this.getListOfLitters();
    }
};