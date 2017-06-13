App.Components.NotificationTab = {
    template: "#notification-tab-template",
    data: function () {
        return {
            activeTasks: [],
            transfers: []
        }
    },
    components: {
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher
    },
    events: {
        'notification-tab-reload-tasks': function (msg) {
            this.load();
        }
    },
    methods: {
        onlyUnseen: objects => objects.filter(object => !object.seen),
        openManagingModal: function (task) {
            if (!task.read) {
                this.toggle(task);
            }

            const router = new VueRouter();

            if (task.type == 'general' || task.icon == 'fa-venus-mars bg-blue' || task.icon == 'fa-check bg-maroon') {
                router.go("/schedule");
                return false;
            }

            if (task.type == "breeder" && task.subtype == "birth") {
                return App.vent.openBirthModal(task.breed_id);
            }

            if (task.type == "litter" && task.subtype == "weigh") {
                return App.vent.weightModal(task.litter_id);
            }

            if (task.type == "litter" && task.subtype == "butch") {
                return App.vent.butcherModal(task.litter_id);
            }
        },
        getFullTaskName: function(task) {
            if (task.holderName) {
                if (task.type == 'breeder') {
                    return task.holderName + ': ' + task.name;
                }
                if (task.type == 'litter') {
                    return 'Litter ' + task.holderName + ': ' + task.name;
                }
            }
            return task.name;
        },
        load() {
            this.$http.get('/admin/notifications').then(response => {
                const data = response.data;
                this.activeTasks = data.events.filter(event => event.closed != '1');
                this.transfers = data.transfers;
                setTimeout(() => { this.load(); }, 4000);
            });
        },
        acceptTransfer(transfer) {
            if (!transfer.read) {
                this.toggle(transfer);
            }

            var router = new VueRouter();

            api.acceptTransfer(transfer).then(data => {
                if (data.result) {
                    router.go(data.result.url.split('/#!').pop());
                }
            });
        },
        toggle(object) {
            object.read = !object.read;
            this.$http.post('/admin/notifications/' + object.notification_id + '/read', { read: object.read });
        },
        opened() {
            const notifications = this.activeTasks.filter(task => !task.seen)
                                    .concat(this.transfers.filter(transfer => !transfer.seen));
            if (notifications.length) {
                this.$http.post('/admin/notifications/seen', { ids: notifications.map(object => object.notification_id) });
                notifications.forEach(notification => {
                    notification.seen = true;
                });
            }
        }
    },
    created: function () {
        this.load();
    }
};

Vue.element('notification-tab', App.Components.NotificationTab);
