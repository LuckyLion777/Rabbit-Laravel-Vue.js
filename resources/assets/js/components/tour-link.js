App.Components.TourLink = {
    template: "<a href=\"#\" @click.prevent=\"startTour('dashboard')\"><i class=\"fa fa-map-signs\"></i> Tour</a>",

    events: {
        'notification-tab-reload-tasks': function (msg) {
            this.load();
        }
    },
    methods: {
        startTour(section){
            var router = new VueRouter();
            router.go({path: '/'});
            $('body').trigger('tour.show');
        }
    }
};

Vue.element('tour-link', App.Components.TourLink);
