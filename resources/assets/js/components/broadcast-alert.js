App.Components.BroadcastAlert = {
    template: "#broadcast-alert-template",
    data() {
        return {
            title: '',
            content: '',
            existing: false,
            loading: null,
            show: true,
        }
    },
    methods: {
        dismiss() {
            this.$http.delete('/admin/broadcast/active').then(() => {
                this.load();
            })
        },
        load() {
            this.$http.get('/admin/broadcast/active').then(response => {
                const broadcast = response.data.broadcast;
                if (this.existing = !!broadcast) {
                    this.title = broadcast.title;
                    this.content = broadcast.content;
                    this.$nextTick(() => {
                        window.dispatchEvent(new Event('resize'));
                    })

                }
                if (this.loading) {
                    window.clearTimeout(this.loading);
                }
                this.loading = window.setTimeout(() => {
                    this.load();
                }, 4000);
            });
        }
    },
    compiled() {
        $('body').on('broadcast.hide', () => this.show = false);
        $('body').on('broadcast.show', () => this.show = true);
    },
    ready() {
        this.load();
    }
};
