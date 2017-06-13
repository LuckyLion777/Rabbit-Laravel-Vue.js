/**
 * Cage cards template form component.
 */
App.Components.CageCardsTemplateForm = {
    template: '#cage-cards-template-form-template',
    data: function () {
        return {
            errors: {},
            type: '',
            size: '',
            orientation: '',
            breederFields: [],
            litterFields: [],
            customFieldsIds: [],
            previewItem: '',
            firstBreeder: {},
            firstLitter: {},
            cageCardPrintModal: {},
            cageCardTemplateFormModal: {}
        };
    },
    props: ['template', 'alltemplates', 'fieldsoptions', 'paginatedtemplates'],
    computed: {
        currentRoute: function () {
            return this.$route.path.split('?')[0];
        }
    },
    methods: {
        /**
         * Get list of fields form DB for breeders and litters.
         */
        getListOfFields: function() {
            api.getCageCardTemplateFieldsOptionsList().then(data => {
                this.breederFields = data.breeder;
                this.litterFields = data.litter;
            });
        },

        /**
         * Get field name in readable format.
         */
        getReadableOptionsFieldName: function(field) {
            var cleared = field.replace(/_id|_/g, ' '),
                decorated = this.fieldNameDecorator(cleared.trim());

            return decorated.substr(0, 1).toUpperCase() + decorated.substr(1);
        },

        /**
         * Add selected field to fields scope.
         */
        addSelectedFieldToFields: function(e) {
            var parts = e.target.value.split(':'),
                id = Number(parts[0]),
                field = parts[1];

            this.template.fields[parts[0]] = parts[1];

            // Custom field.
            var customFieldIdIndex = this.customFieldsIds.indexOf(id);

            if (field == 'custom') {
                // Add custom field to arr.
                if (customFieldIdIndex == -1) {
                    this.customFieldsIds.push(id);
                } // Remove custom field.
            } else if (customFieldIdIndex !== -1) {
                this.customFieldsIds.splice(customFieldIdIndex, 1);
            }
        },

        /**
         * Depending on type we set different set of fields options for template form.
         */
        setFieldsOptions: function(e) {
            var type = e.target.value,
                fieldsArr = type + 'Fields';

            this.fieldsoptions= this[fieldsArr];

            // Reset fields.
            this.resetSelectableFieldsStatement();
        },

        /**
         * Reset selectable fields scope if we changed type or size.
         */
        resetSelectableFieldsStatement: function() {
            App.vent.trigger('resetSelectableFieldsStatement');

            this.template.fields = {};
            this.customFieldsIds = [];
        },

        /**
         * Store cage card template.
         */
        storeCageCardTemplate: function() {
            var template = this.template;

            api.storeCageCardTemplate(template).then(
                data => {
                    App.vent.trigger('cageCardTemplate.saved');

                    $('#cage-cards-template-form-modal').modal('hide');

                    // Add new template to paginated and all templates scopes.
                    this.alltemplates.unshift(data);

                    if (this.currentRoute == '/cage-cards/templates') {
                        this.paginatedtemplates.unshift(data);
                    }

                    // Go to first page.
                    this.$router.go('/cage-cards/templates');
                },
                response => {
                    this.errors = response.data;
                }
            );
        },

        /**
         * Update cage card template.
         */
        updateCageCardTemplate: function() {
            var scope = this,
                template = scope.template;

            api.updateCageCardTemplate(template).then(
                data => {
                    App.vent.trigger('cageCardTemplate.updated');

                    $('#cage-cards-template-form-modal').modal('hide');

                    // Update alltemplates and paginated templates scopes.
                    $.each([scope.paginatedtemplates, scope.alltemplates], function(i, templates) {
                        var match = _.find(templates, function (item) {
                            return item.id === template.id
                        });

                        if (match) {
                            _.extendOwn(match, data)
                        }
                    });

                    scope.template = data;
                },
                response => {
                    scope.errors = response.data;
                }
            );
        },

        /**
         * Check whether select fields is selected.
         *
         * @param field
         * @param id
         */
        isSelectFieldValue: function(field, id) {
            return this.template.fields[id] !== undefined && this.template.fields[id].indexOf(field) > -1;
        },

        /**
         * Check whether custom field is selected.
         *
         * @param id
         */
        isCustomFieldVisible: function(id) {
            return this.customFieldsIds.indexOf(id) > -1 || (this.template.fields[id] && this.template.fields[id].indexOf('custom') > -1);
        },

        /**
         * Get custom field value.
         *
         * @param id
         */
        getCustomFieldValue: function(id) {
            var val = '';

            if (this.isCustomFieldVisible(id)) {
                val = this.template.fields[id].split(':')[1];
            }

            return val;
        },

        /**
         * Set custom field.
         *
         * @param id
         * @param value
         */
        setCustomField: function(id, value) {
            this.template.fields[id] = 'custom:' + value;
        },

        /**
         * Get preview.
         */
        getPreview: function() {
            var size = this.template.size,
                type = this.template.type;

            App.vent.trigger('cageCardTemplate.preview');

            this.previewItem = [type, size].join('.');
        },

        /**
         * Close modal handler.
         */
        closeModalHandler: function() {
            App.vent.trigger('closeModal');
        },

        /**
         * Field name decorator.
         *
         * @param fieldName
         */
        fieldNameDecorator: function(fieldName) {
            var map = {};

            // Breeder.
            if (this.template.type == 'breeder') {
                map = {
                    'tattoo': 'id',
                    'aquired': 'acquired',
                };
            }

            // Litter.
            if (this.template.type == 'litter') {
                map = {
                    'given': 'id',
                };
            }

            if (map[fieldName]) {
                fieldName = map[fieldName];
            }

            return fieldName;
        },

        /**
         * Get preview field name.
         *
         * @param model
         * @param id
         * @param defaultValue
         */
        getPreviewFieldName: function(model, id, defaultValue) {
            var name = '',
                fieldName = this.template.fields[id] !== undefined ? this.template.fields[id] : '';

            if (!$.isEmptyObject(model)) {
                if (model[fieldName] !== undefined && model[fieldName]) {
                    name = this.getReadableOptionsFieldName(fieldName);
                }

                // Custom or blank fields.
                if (fieldName == 'custom' || fieldName == 'blank') {
                    name = '';
                }
            } else if (defaultValue !== undefined && defaultValue.length) {
                name = defaultValue;
            }

            return name.length ? name + ': ' : '';
        },

        /**
         * Get preview field value.
         *
         * @param model
         * @param id
         * @param defaultValue
         */
        getPreviewFieldValue: function(model, id, defaultValue) {
            var value = '',
                fieldName = this.template.fields[id] !== undefined ? this.template.fields[id] : '';

            if (!$.isEmptyObject(model)) {
                if (model[fieldName] !== undefined && model[fieldName]) {
                    value = model[fieldName];

                    // Get value for relation.
                    if (fieldName.indexOf('_id') > -1) {
                        var relation = fieldName.split('_id')[0];

                        if (model[relation] !== undefined && model[relation].name !== undefined) {
                            value = model[relation].name;
                        }
                    }

                    // Litters special fields.
                    if (this.template.type == 'litter') {
                        // Mother and father.
                        if ($.inArray(fieldName, ['mother', 'father']) > -1) {
                            value = value.length && value[0].name !== undefined ? value[0].name : '';
                        }

                        // Given id.
                        if (fieldName == 'given_id') {
                            value = model[fieldName]
                        }
                    }
                }

                // Custom.
                if (fieldName.indexOf('custom') > -1) {
                    value = fieldName.split(':')[1];
                }

                // Blank.
                if (fieldName == 'blank') {
                    value = '<p></p>';
                }
            } else if (defaultValue.length) {
                value = defaultValue;
            }

            return value;
        },

        /**
         * Open print modal.
         */
        printModal: function(template) {
            this.cageCardTemplateFormModal.modal('hide');
            this.template = _.extend({}, template);
            this.cageCardPrintModal.modal('show');
        },

        /**
         * Listeners.
         */
        listeners: function() {
            // Reset selectable fields statement event handler.
            App.vent.on('resetSelectableFieldsStatement', () => {
                this.previewItem = '';
            });

            // Close modal event handler.
            App.vent.on('closeModal', () => {
                this.previewItem = '';
                this.errors = {};
                this.resetSelectableFieldsStatement();
            });
        }
    },
    ready: function() {
        var scope = this;

        // Jquery elements assignment.
        scope.cageCardPrintModal = $('#cage-cards-print-modal');
        scope.cageCardTemplateFormModal = $('#cage-cards-template-form-modal');

        // Listeners for triggers.
        scope.listeners();

        // Get list of fields for breeder and litter.
        scope.getListOfFields();

        // Get first entities data.
        api.getFirstEntitiesForPreview().then(
            data => {
                this.firstBreeder = data.breeder;
                this.firstLitter  = data.litter;
            },
            response => {
                this.errors = response.data;
            }
        );
    }
};