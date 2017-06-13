App.Components.Dashboard = {
    template: "#dashboard-template",
    data: function () {
        return {
            litter: {},
            litters: [],
            kits: [],
            breeders: [],
            activeBreed: {},
            activeBirth: {},
            plans: [],
            dummyEvents: []
        }
    },
    props: [],
    computed: {

    },
    components: {
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher,
        'upcomming-tasks': App.Components.UpcommingTasks,
        'schedule-calendar': App.Components.ScheduleCalendar,
        'birth-form': App.Components.BirthForm
    },
    events: {
        'reload-tasks': function(msg){
            this.$broadcast('upcomming-tasks-reload', msg);
            this.$broadcast('schedule-calendar-reload', msg);
        },
        'task-managing': function(msg){
            this.$broadcast('upcoming-tasks-managing',msg);
        },
        'open-breed-modal': function(msg){
            this.$broadcast('new-plan-with-doe', msg);
        }
    },
    watch: {},
    methods: {
    	triggerLedgersFileInput: function() {
    		console.log('This will trigger file input');
    	},
    	
        newLitter: function () {
            this.$route.router.go({
                name: 'litters',
                params: { action: 'new' }
            });
        },

        newBreeder: function () {
            this.$route.router.go({
                name: 'breeders',
                params: { action: 'new' }
            });
        },
        shortDate: function (date){
            return App.parseMoment(date).format(App.dateFormatShort);
        },
        breedModal: function () {
            this.$broadcast('new-plan');
        },
        birthModal: function (plan_id) {
            this.activeBirth = {breedplan: plan_id || '-1', born: moment(new Date()).format(App.dateFormat)};
            this.$broadcast('new-birth-modal-open');
            api.getUsersPlans().then(plans => {
                this.plans = plans;
                $('#birth-form-modal').modal('show');
            });
            delete App.dashboard_birth_breed;
        },

        weightModal: function (litter_id) {
            this.litter = litter_id
                            ? this.litters.find(litter => litter.id == litter_id)
                            : this.litters[0];
            $('#litter-weight-modal').modal('show');
            delete App.dashboard_weight_litter;
        },

        butcherModal: function (litter_id) {
            this.litter = litter_id
                            ? this.litters.find(litter => litter.id == litter_id)
                            : this.litters[0];
            $('#litter-butcher-modal').modal('show');
            delete App.dashboard_butch_litter;
        },
        loadBreeders: function(){
            api.getBreedersList().then(breeders => {
                this.breeders = breeders;
            });
        },

        startTour(section){
            this.$http.get('/js/tours/steps.json').then((response) => {
                var intro = introJs();
                intro.setOptions({
                    steps: response.data[section],
                    showBullets: false
                });
                intro.start();
            });
        }

    },
    ready: function () {
        api.getLitters({ page: 1, archived: false }).then(res => {
            this.litters = res.litters.data;
            if (App.dashboard_birth_breed) {
                this.birthModal(App.dashboard_birth_breed);
            }
            if (App.dashboard_butch_litter) {
                this.butcherModal(App.dashboard_butch_litter);
            }
            if (App.dashboard_weight_litter) {
                this.weightModal(App.dashboard_weight_litter);
            }
            App.vent.on('dashboard-birth-breed', (el, breed_id) => {
                this.birthModal(breed_id);
            });
            App.vent.on('dashboard-butch-litter', (el, litter_id) => {
                this.butcherModal(litter_id);
            });
            App.vent.on('dashboard-weight-litter', (el, litter_id) => {
                this.weightModal(litter_id);
            });
        });
        this.$emit('reload-tasks', {typeOfTask: 'all'});
        $('body').off('tour.show').on('tour.show', () => {
            this.startTour('dashboard');
        });
        if(!App.tourShowed){
            // this.startTour('dashboard');
            App.tourShowed = 1;
            this.$http.post('/admin/users/tourShowed');
            $('body').trigger('tour.show');
        }

    },
    destroyed() {
        App.vent.off('dashboard-birth-breed');
        App.vent.off('dashboard-butch-litter');
        App.vent.off('dashboard-weight-litter');
    },
    mixins: [App.Mixins.Butcherable]
};
