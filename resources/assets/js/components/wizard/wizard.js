App.Components.Wizard = {
    template: "#wizard-template",
    data: function () {
        window.NotificationManager.subscribe();
        return {
            user: {},
            chains: [],
            user_id: "",
            success: {},
            errors: {},
            loaded: false,
            digest_enabled: false,
            temp_digest_day: -1,
            success_general: {},
            success_schedule: {},
            success_pedigree: {},
            iconBackground: {
                'bg-red': false, 'bg-blue': false, 'bg-maroon': false, 'bg-green': false, 'bg-yellow': false,
                'bg-grey': false, 'bg-purple' : false, 'fa-calendar' : false, 'fa-heart' : false,
                'fa-asterisk' : false, 'fa-bookmark' : false, 'fa-eye' : false, 'fa-flag' : false,
                'fa-medkit' : false, 'fa-paw' : false, 'fa-trophy' : false, 'fa-inbox' : false
            }
        }
    },
    props: [],
    computed: {

    },
    watch:{

    },
    methods: {

    },
    ready: function () {


    },

    route: {
        activate: function () {
            $('body').addClass('sidebar-collapse');
            $('.content').css({overflow: 'hidden'});
            $('body').trigger('broadcast.hide');
            $('#callout-trial').hide();
        },
        deactivate: function (transition) {
            $('body').removeClass('sidebar-collapse');
            $('.content').css({overflow: 'auto'});
            $('body').trigger('broadcast.show');
            $('#callout-trial').show();
            transition.next();
        }
    }
};
