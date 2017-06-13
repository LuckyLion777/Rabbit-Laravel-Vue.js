App.Components.BreederCategoryForm = App.Components.Exploitable.Form.extend({
    template: "#breeder-category-form-template",
    data: function () {
        return {
            modal_id: '#breeder-category-form',
            emptyModel: {
                description: ''
            },
            category: {},
            saver: api.saveBreederCategory
        }
    },
    computed: {
        model: {
            get: function() {
                return this.category;
            },
            set: function(model) {
                this.category = model;
            }
        }
    },
    watch: {
        category: function() {
            $('#category-name').typeahead('val', this.category.name);
        }
    },
    methods: {
        initTypeahead: function() {
            api.getBreederCategoriesAutocomplete().then(data => {
                var names = data.name;
                $('#category-name').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 0
                }, {
                    source: function(req, callback) {
                        callback(names.filter(function(name) {
                            return name.toLowerCase().indexOf(req.toLowerCase()) !== -1;
                        }));
                    }
                });
            });
        }
    },
    ready: function () {
        this.initTypeahead();
    }
});
