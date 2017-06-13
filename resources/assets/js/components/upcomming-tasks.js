App.Components.UpcommingTasks = {
    template: "#upcomming-tasks-template",
    data: function () {
        return {
            loading: 1,
            activeTasks: [],
            plans: [],
            activeTask: {},
            activePlan: {},
            originalTask:{},
            rabbits: [],
            breeder_litter: [],
            dummyEvents: [],
            breeders: [],
            planBreeders: { bucks:[], does: []},
            week: [],
            label : 'label',
            label_color : 'label-danger',
            type_of_task : 'all',
            current_page : null,
            last_page : null,
            perPage : 10,
            pagination_page: [],
            activeTasksForOneRabbit : [],
            activeRabbit: {},
            info_panel_class : ' bg-info',
            button_class : ' btn-primary',
            name_of_modal: 'New Task',
            modeOfTask: 'create',
            errors: { name: null, date: null, icon: null},
            type_of_tasks: 'all',
            iconBackground: {
                'bg-red': false, 'bg-blue': false, 'bg-maroon': false, 'bg-green': false,
                'bg-yellow': false, 'bg-grey': false, 'fa-calendar' : false, 'fa-heart' : false,
                'fa-asterisk' : false, 'fa-bookmark' : false, 'fa-eye' : false, 'fa-flag' : false,
                'fa-medkit' : false, 'fa-paw' : false, 'fa-trophy' : false, 'bg-purple': false,
                'bg-orange': false
            },

            searchFields: ['name', 'holderName']
        }
    },
    props: ['externalTasks'],
    computed: {},
    watch:{
        activePlan: function(){
            this.resetArchivedFilterBreeders();
        },
        'activePlan.doe': function(){
            this.setFilterBreeders(this.activePlan.doe, this.activePlan.buck);
        },

        'activePlan.buck': function(){
            this.setFilterBreeders(this.activePlan.doe, this.activePlan.buck);
        },

        activeTask: function(){
            this.resetArchivedFilterBreeders();
        },
        'activeTask.relation.id': function(value){
            this.setFilterBreedersEntry(value);
        },

        'activePlan.date': function(newValue, oldValue){
            if(newValue == ''){
                this.tempDate = oldValue;
            }
            if(oldValue == ''){
                oldValue = this.tempDate;
            }
            if(this.activePlan.id){
                if (newValue && oldValue){
                    var date = App.formatDate(newValue);
                    var oldDate = App.formatDate(oldValue);
                    var diff = date.diff(oldDate, 'days');
                    _.each(this.dummyEvents, function (item) {
                        item.date = App.parseMoment(item.date).add(diff, 'days').format(App.dateFormat)
                    });
                }
            } else {
                if(newValue){
                    this.generateDummyEvents(newValue);
                }
            }

        },
        'activeTask.type': function(newValue, oldValue){
            switch(newValue){
                case 'breeder': {this.loadBreeders(); break;}
                case 'litter': {this.loadLitters(); break;}
                case 'plans': {this.loadPlans(); break;}
                default:{this.breeder_litter = [];}
            }
        },
        'externalTasks': function (newValue, oldValue) {
            this.activeTasks = newValue;
        },
        'activeTask.icon': function(newValue, oldValue){
            if(newValue){
                var newClass = newValue.split(' ')[1];
                var oldClass = oldValue ? oldValue.split(' ')[1] : '';

                if(newClass == 'bg-gray') newClass = newValue.split(' ')[0];
                if(oldClass == 'bg-gray') oldClass = oldValue ? oldValue.split(' ')[0] : '';

                this.iconBackground[newClass] = true;
                if(oldValue) this.iconBackground[oldClass] = false;
            }
        },
        searchQuery: function(){
            this.loadTasks();
        }
    },
    events: {
        'upcoming-tasks-managing': function (task) {
            this.openTaskForm(task);
        },
        'upcomming-tasks-reload': function(msg){
            this.loadTasks(msg.typeOfTask, msg.page);
        },
        'type-events-was-changed': function(msg){
            this.type_of_task = msg.typeOfTask;
            this.$dispatch('upcomming-tasks-reload', msg)

        },
        'upcomming-tasks-delete-task': function(msg){
            this.showRequestForm(msg);
        },
        'upcomming-tasks-new-task' : function(){
            this.openTaskForm();
        },

        'new-plan': function(){
            this.openPlanForm();
        },

        'new-plan-with-doe': function(doeId){
            this.openPlanForm(null, doeId);
        },

        doMissedModel: function(plan) {
            api.missBirth({ breedplan: this.activePlan.id }).then(() => {
                $('#missing-modal-plan').modal('hide');
                $('#breed-edit').modal('hide');
                this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: null});
            });
        }
    },
    methods: {
        shortDate: function (date) {
            return App.parseMoment(date).format(App.dateFormatShort);
        },

        capitalize : function (string) {
            return string.replace(/^./, function (match) {
                return match.toUpperCase();
            });
        },
        updateSettings: function () {},
        getTypeId: function(type){
            var types = {'general' : 1, 'litter' : '2','breeder' : 3};
            return types[type];
        },
        updatePlan: function(plan){
            this.loading = 1;
            api.savePlan(plan).then(
                () => {
                    this.loading = 0;
                    $('#breed-edit').modal('hide');
                    this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: null});
                },
                response => {
                    this.loading = 0;
                    this.errors = response.data;
                }
            );
        },
        generateDummyEvents: function(date) {
            this.loading = 1;
            api.getDummyEvents(date).then(events => {
                this.loading = 0;
                this.dummyEvents = events;
            });
        },
        loadPlanEvents: function(plan) {
            this.loading = 1;
            api.getPlanEvents(plan.id).then(events => {
                this.loading = 0;
                this.dummyEvents = events;
            });
        },
        openPlanForm: function(plan, doeId){
            let buckDefaultId = -1;
            let doeDefaultId = doeId? doeId: -1;
            this.loading = 1;
            api.getPlansBreeders().then(data => {
                this.loading = 0;
                this.planBreeders = data;
                this.errors = [];
                var today = moment(new Date()).format(App.dateFormat);
                this.dummyEvents = [];

                if(plan) {
                    this.info_panel_class = "bg-info";
                    this.name_of_modal = "Schedule Breed";
                    this.button_class = "btn-primary";
                    this.activePlan = plan;
                    var doe = this.activePlan.breeders.filter(function(breeder){
                        return breeder.sex == 'doe';
                    }).shift();
                    if(doe){
                        this.activePlan.doe = doe.id;
                    }
                    var buck = this.activePlan.breeders.filter(function(breeder){
                        return breeder.sex == 'buck';
                    }).shift();
                    if(buck){
                        this.activePlan.buck = buck.id;
                    }
                    this.setFilterBreeders(this.activePlan.doe, this.activePlan.buck);
                    this.loadPlanEvents(plan);
                } else {
                    this.generateDummyEvents(today);
                    this.info_panel_class = "bg-success";
                    this.name_of_modal = "Schedule Breed";
                    this.activePlan = {doe: doeDefaultId, buck: buckDefaultId, date: moment(new Date()).format(App.dateFormat)};
                }
                $('#breed-edit').modal('show');
            });

        },
        openTaskForm: function(task){
            this.$resetValidation();
            if(task){
                this.info_panel_class = "bg-info";
                this.name_of_modal = "Edit Task";
                this.button_class = "btn-primary";
                this.modeOfTask = 'edit';
                this.loading = 1;
                api.getEvent(task.id).then(task => {
                    this.loading = 0;
                    this.activeTask = $.extend({}, task);
                    this.originalTask = $.extend({}, task);
                    this.activeTask.relation = {};
                    if (this.activeTask.litters || this.activeTask.breeders) {
                        this.activeTask.relation = this.activeTask.litters
                                                        ? this.activeTask.litters[0]
                                                        : this.activeTask.breeders[0];
                    }
                });

            } else {
                this.modeOfTask = 'create';
                this.name_of_modal = "Create Task";
                this.info_panel_class = "bg-success";
                this.button_class = "btn-success";
                this.errors = { name: null, date: null, icon: null};
                this.activeTask = {
                    'name': null,'date': App.formatDate(new Date), 'recurring': "1",
                    'type': "general",'icon' : 'fa-cutlery bg-red', relation: {id: ''}
                };
            }

            $('#new_task').modal('show');
            $('#more_tasks').modal('hide');
        },
        createTask: function(mode){
            this.$validate(true);
            if(this.$activeTaskValidator.touched && this.$activeTaskValidator.invalid) return;
            this.loading = 1;
            if (mode == 'edit') {
                this.activeTask.type_id = this.activeTask.relation.id || null;
                this.activeTask.type_changed = (this.originalTask.type !== this.activeTask.type) ||
                                                (this.originalTask.type_id !== this.activeTask.type_id);
                api.saveEvent(this.activeTask).then(() => {
                    if(this.activeTask.closed == '0'){
                        api.reopenEvent(this.activeTask);
                    } else {
                        api.closeEvent(this.activeTask);
                    }
                    this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    this.loading = 0;
                    $('#new_task').modal('hide');
                });
            } else {
                this.activeTask._method = "POST";
                this.activeTask.type_id = this.activeTask.relation ? this.activeTask.relation.id : null;
                api.saveEvent(this.activeTask).then(() => {
                    this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    this.loading = 0;
                    $('#new_task').modal('hide');
                });
            }
        },
        finishTask: function(task){
            if(task.closed == '1'){
                api.reopenEvent(task).then(() => {
                    task.closed = '0';
                });
            } else {
                api.closeEvent(task).then(() => {
                    task.closed = '1';
                });
            }
        },
        loadTasks: function (typeOfTask, page) {
            if(!typeOfTask){typeOfTask = this.type_of_task;}
            if(typeOfTask == 'plans'){
                this.loadPlans(page);
                return;
            }
            var filter = {};


            if (typeOfTask == 'archived') {
                filter.type = 'general,litter,breeder';
                filter.archived = 'true';
                filter.expired = 'true';
            } else if(typeOfTask == 'expired'){
                filter.type = 'general,litter,breeder';
                filter.expired = 'true';
            } else {
                switch(typeOfTask){
                    case 'general':
                    case 'breeder':
                    case 'litter':
                        filter.type = typeOfTask;
                        break;
                    default:
                        filter.type = 'general,litter,breeder'
                }
            }

            filter.perPage = this.perPage;
            filter.page = page;

            filter.searchQuery = this.searchQuery;
            this.loading = 1;
            api.getEvents(filter).then(tasks => {
                this.loading = 0;
                this.activeTasks = tasks.data;
                this.current_page = tasks.current_page;
                this.last_page = tasks.last_page;
                this.makeTaskPaginationData(this.last_page);
            }, () => {});
        },
        loadPlans: function (page) {
            this.loading = 1;
            api.getPlans({ perPage: this.perPage, page: page }).then(plans => {
                this.loading = 0;
                this.plans = plans['data'];
                this.current_page = plans['current_page'];
                this.last_page = plans['last_page'];
                this.makeTaskPaginationData(this.last_page);
            }, () => {});
        },
        getFullTaskName: function(task){
            if(task.holderName){
                if(task.type == 'breeder'){
                    return task.holderName + ': ' + task.name;
                }
                if(task.type == 'litter'){
                    return 'Litter ' + task.holderName + ': ' + task.name;
                }
            }
            return task.name;
        },

        unarchiveTask: function(task){
            if(task && task.id) {
                task.archived = 0;
                api.unarchiveEvent(task).then(() => {
                    var msg = {};
                    $('#id_task_' + task.id).slideUp(200);
                    $("#unarchive_task").modal('hide');
                    this.activeTask = {};
                    msg.typeOfTask = this.type_of_task;
                    this.$dispatch('reload-tasks', msg);
                });
            }
        },
        deleteTask : function(task) {
            if (task && task.id) {
                api.deleteEvent(task).then(() => {
                    var msg = {};
                    $('#id_task_'+task.id).slideUp(200);
                    $("#delete_task").modal('hide');
                    this.activeTask = {};

                    msg.typeOfTask = this.type_of_task;
                    this.$dispatch('reload-tasks', msg);
                });
            } else {
                var tasksForDelete = [];
                // this.activeTasks.forEach(function(item, i, arr){
                //     if(item.closed == 1) tasksForDelete.push(item.id);
                // });
                api.archiveEvents(tasksForDelete).then(() => {
                    var msg = {typeOfTask: this.type_of_task};
                    $("#archive_task").modal('hide');
                    this.$dispatch('reload-tasks', msg);
                });
                // if(tasksForDelete.length){
                //     api.archiveEvents(tasksForDelete).then(() => {
                //         var msg = {typeOfTask: this.type_of_task};
                //         $("#archive_task").modal('hide');
                //         this.$dispatch('reload-tasks', msg);
                //     });
                // } else {
                //     var msg = {typeOfTask: this.type_of_task};
                //     $("#archive_task").modal('hide');
                //     this.$dispatch('reload-tasks', msg);
                // }
            }
        },
        deletePlan : function(plan){
            if(plan && plan.id){
                api.deletePlan(plan).then(() => {
                    $('#id_plan_'+plan.id).slideUp(200);
                    $("#delete_plan").modal('hide');
                    this.activePlan = {};
                });
            }
        },
        loadBreeders: function() {
            this.loading = 1;
            api.getBreedersList().then(breeders => {
                this.loading = 0;
                this.breeder_litter = breeders.bucks.concat(breeders.does);
                this.breeders = breeders;
            });
        },
        loadLitters: function(){
            this.loading = 1;
            api.getLittersList().then(litters => {
                this.loading = 0;
                var tempRabbits = [];
                litters.forEach(function(item) {
                    var name = item.parents.map(function (elem) {
                        return elem.name;
                    }).join("/");
                    tempRabbits.push({'given_id' : item.given_id, 'id' : item.id, 'name' : name});
                });
                this.breeder_litter = tempRabbits;
            });
        },

        getTimeLeft: function(task){
            var oToday = moment().startOf("day").utcOffset(0);
            var oDeadLineDate = App.parseMoment(task.date);
            var nDaysLeft = oDeadLineDate.diff(oToday, "days");
            if(!nDaysLeft) return 'today';
            var text = Math.abs(nDaysLeft) + ' days';
            if(Math.abs(nDaysLeft) == 1) text = Math.abs(nDaysLeft) + ' day';
            if(nDaysLeft < 0) {
                //text = text + " ago";
                text = "expired";
            }
            return text;
        },
        getColorForDate: function(task){
            var labelClass = "label ";
            //start time : from today
            //within 1 days:red.
            var dangerDay = 1; // label-danger
            //within 2-7 days : yellow.
            var warningDay = 7; //label-warning
            //1-2 weeks : primary blue,
            //var infoDay = 14; //label-info
            //3-4 weeks : blue,
            //var primaryDay = 28; //label-primary
            //all beyond : green. label-success

            var oToday = new Date();
            var oDeadLineDate = new Date(task.date);
            var nDaysLeft = oDeadLineDate > oToday ? Math.ceil((oDeadLineDate - oToday) / (1000 * 60 * 60 * 24)) : null;

            if(nDaysLeft <= dangerDay){
                return labelClass + "label-danger";
            } else if(nDaysLeft <= warningDay){
                return labelClass + "label-warning";
            } else {
                return false;
            }
        },
        makeTaskPaginationData: function(last_page){
            if(last_page === 1){
                this.pagination_page =[];
                return;
            }
            this.pagination_page =[];
            for(var i=1; i <= last_page; i++){
                this.pagination_page.push(i);
            }
        },
        showRequestForm: function(task){
            this.activeTask = task;
            if(task){
                $('#delete_task').modal('show');
            } else {
                $('#archive_task').modal('show');
            }
        },
        showPlanDeletingForm: function(plan){
            this.activePlan = plan;
            $('#delete_plan').modal('show');

        },
        showUnarchiveForm: function(task) {
            this.activeTask = task;
            if (task) {
                $('#unarchive_task').modal('show');
            }
        },
        showMissed: function(plan) {
            if (plan) {
                $('#missing-modal-plan').modal('show');
            }
        },
        missed: function() {
            this.$emit('doMissedModel');
        },
        filterTasksBreeders: function (value) {
            if (this.withArchivedBreeders) {
                return true;
            }
            return this.activeTask.relation && this.activeTask.relation.id == value.id
                        || value.archived === 0 && value.sold_at == null;
        },
    },
    ready: function () {
        FastClick.attach(document.getElementById('todo-list-fastclick'));
    },

    mixins: [App.Mixins.BreedersFilter, App.Mixins.Filterable]
};
