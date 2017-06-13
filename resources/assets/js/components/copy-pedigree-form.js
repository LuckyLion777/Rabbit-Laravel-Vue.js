App.Components.CopyPedigreeForm = {
    template: "#copy-pedigree-form-template",
    data() {
        return {
            options: [],
            copyId: '',
            withArchived: false,
            line: 'both'
        }
    },
    props: ['type', 'id', 'name'],
    methods: {
        copy() {
            api.copyPedigree(this.type, this.copyId, this.id, this.line).then(() => {
                $('#copy-pedigree-form').modal('hide');
                this.$dispatch('load_pedigree');
            });
        },
        load() {
            api.getOptionsForCopyPedigree(this.type, this.id, { archived: this.withArchived }).then(options => {
                this.options = options;
            });
        }
    },
    watch: {
        withArchived() {
            this.load();
        },
        type() {
            this.load();
        },
        id() {
            this.load();
        }
    },
    created() {
        this.load();
    }
};
