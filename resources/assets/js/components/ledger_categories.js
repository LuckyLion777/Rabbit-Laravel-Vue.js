App.Components.LedgerCategories = App.Components.Exploitable.Section.extend({
    template: "#category-template",
    data: function() {
        return {
            categories: [],
            hasArchive: false,
            loading: 1,
            deleter: api.deleteLedgerCategory
        };
    },        
    computed: {
        models: function() {
            return this.categories;
        }
    },
    components: {
        'category-form': App.Components.LedgerCategoryForm
    },
    methods: {
        updateList: function () {
            api.getLedgerCategories().then(data => {
                this.loading = 0;
                this.categories = data.categories;
            });
        }
    },
    ready: function () {
        this.updateList();
    }
});
