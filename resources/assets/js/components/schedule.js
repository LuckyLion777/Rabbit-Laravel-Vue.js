App.Components.Schedule = {
    template: "#schedule-template",
    data: function () {
        return {
            activeTasks: [],
            perPage: 10,
            litters: [],
            plans: [],
            activeBirth: {},
            type_of_task: 'all'
    }
    },
    props: [],
    computed: {
        filter: function() {
            return this.$route.params.filter;
        }
    },
    components: {
        'upcomming-tasks': App.Components.UpcommingTasks,
        'schedule-calendar': App.Components.ScheduleCalendar,
        'notification-tab': App.Components.NotificationTab,
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher
    },
    watch:{
        filter: function () {
            this.changeTypeForFilter(this.filter);
        }
    },
    events:{
        'task-managing': function(msg){
            this.$broadcast('upcoming-tasks-managing',msg);
        },
        'schedule-calendar-reload': function(msg){
            this.$broadcast('schedule-calendar-filtering', msg);
        },
        'reload-tasks': function(msg){
            this.$broadcast('upcomming-tasks-reload', msg);
            this.$broadcast('schedule-calendar-reload', msg);
        },
        'type-events-was-changed':function(msg){
            this.$broadcast('type-events-was-changed', msg);
        },
        'events-for-deleting':function(msg){
            this.$broadcast('upcomming-tasks-delete-task', msg);
        }
    },
    methods: {
        changeTypeForFilter: function (typeOfTask, page) {
            this.type_of_task = typeOfTask;
            this.$dispatch('type-events-was-changed', {typeOfTask: typeOfTask, page: page});
        },

        activeItemClass: function (typeOfTask) {
            if(this.type_of_task == typeOfTask) {
                return 'bold';
            }
        },
        lostLitters: function () {
            var data = { page: this.page };
            data.archived = this.filter == "archive" ? 1 : 0;

            api.getLitters(data).then(litters => {
                this.loading = 0;
                this.litters = litters.data;
                this.pages = litters.last_page;
            });
        },
        birthModal: function () {
            api.getUsersPlans().then(plans => {
                this.plans = plans;
            });
            this.activeBirth = {breedplan: '-1', born: moment(new Date()).format(App.dateFormat)};
        },
        recordBirth: function () {
            api.recordBirth(this.activeBirth).then(() => {
                this.activeBirth = {};
                $('#birth').modal('hide');
            });
        },

        openTaskForm: function (){
            this.$broadcast('upcomming-tasks-new-task');
        }


    },
    ready: function () {
        var self = this;
        this.lostLitters();
        this.birthModal();
        if(this.$route.query.new){
            this.openTaskForm();
        }
        this.$watch('$route.query.new', function(){
            if(this.$route.query.new){
                this.openTaskForm();
            }
        });
        $(document).on('hide.bs.modal', '#new_task', function(event){
            if($(event.target).is('#new_task') && self.$route.query.new) {
                self.$route.router.go('/schedule');
            }
        });
        this.changeTypeForFilter(this.filter);
    },
    mixins: [App.Mixins.Butcherable]
};
