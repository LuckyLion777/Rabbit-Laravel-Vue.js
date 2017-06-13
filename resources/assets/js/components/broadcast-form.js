App.Components.BroadcastForm = {
    template: "#broadcast-template",
    data() {
        return {
            title: '',
            content: '',
            existing: false,
            errors: {}
        }
    },
    methods: {
        saveNew() {
            this.errors = {};
            this.$http.post('/admin/broadcast', { title: this.title, content: this.content }).then(
                () => {
                    $('#broadcast-modal').modal('hide');
                },
                response => {
                    this.errors = response.data;
                }
            );
        },
        update() {
            this.errors = {};
            this.$http.put('/admin/broadcast', { title: this.title, content: this.content }).then(
                () => {
                    $('#broadcast-modal').modal('hide');
                },
                response => {
                    this.errors = response.data;
                }
            )
        },
        delete() {
            this.errors = {};
            this.$http.delete('/admin/broadcast').then(
                () => {
                    $('#broadcast-modal').modal('hide');
                },
                response => {
                    this.errors = response.errors;
                }
            )
        }
    },
    ready() {
        $(() => {
            $('#broadcast-modal-trigger').click(e => {
                e.preventDefault();
                this.$http.get('/admin/broadcast').then(response => {
                    if (this.existing = response.data.exists) {
                        this.title = response.data.title;
                        this.content = response.data.content;
                    } else {
                        this.title = this.content = '';
                    }
                    $('#broadcast-modal').modal('show');
                });
            });
        });
    }
};
