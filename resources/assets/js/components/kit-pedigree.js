App.Components.KitPedigree = Vue.extend(App.Components.Pedigree).extend({
    template: '#pedigree-kit-template',
    data: function() {
        return {
            id: this.$route.params.id,
            token: ''
        };
    },
    methods: {
        loadLitters: function () {
            api.getKit(this.id).then(data => {
                this.token = data.token;
            });
            api.getKitPedigree(this.id).then(data => {
                this.generations = data;
            });
        }
    },
    ready: function() {
        var that = this;
        this.loadLitters();
        $('#pedigree-form, #copy-pedigree-form').on('hidden.bs.modal', function (e) {
            // do something...
            that.loadLitters();
        })
    }
});
