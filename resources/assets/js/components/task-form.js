App.Components.TaskForm = {
    template: "#task-form-template",
    data: function () {
        return {
            originalTask:{},
            rabbits: [],
            breeder_litter: [],
            week: [],
            label : 'label',
            label_color : 'label-danger',
            activeRabbit: {},
            info_panel_class : ' bg-info',
            button_class : ' btn-primary',
            name_of_modal: 'New Task',
            modeOfTask: 'create',
            errors: { name: null, date: null, icon: null},
            type_of_tasks: 'all',
            iconBackground: {
                'bg-red': false, 'bg-blue': false, 'bg-maroon': false, 'bg-green': false,
                'bg-yellow': false,'bg-grey': false, 'fa-calendar' : false, 'fa-heart' : false,
                'fa-asterisk' : false, 'fa-bookmark' : false, 'fa-eye' : false, 'fa-flag' : false,
                'fa-medkit' : false, 'fa-paw' : false, 'fa-trophy' : false, 'bg-purple': false
            },
            loading: 1,
        }
    },
    props: {
        activeTask:{
            type: Object,
            default: function () {
                return {}
            }
        }
    },
    computed: {},
    watch:{
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
        'activeTask.type': function(newValue, oldValue){
            switch(newValue){
                case 'breeder': {this.loadBreeders(); break;}
                case 'litter': {this.loadLitters(); break;}
                default:{this.breeder_litter = [];}
            }
        }
    },
    events: {
        'tasks-managing': function (task, newTask) {
            this.openTaskForm(task, newTask);
        },
        'tasks-delete-task': function(msg){
            this.showRequestForm(msg);
        },
        activeTask: function(){
            this.resetArchivedFilterBreeders();
        },
        'activeTask.relation.id': function(value){
            this.setFilterBreedersEntry(value);
        }
    },
    methods: {
        filterTasksBreeders: function (value) {
            if (this.withArchivedBreeders) {
                return true;
            }
            return this.activeTask.relation && this.activeTask.relation.id == value.id
                        || value.archived === 0 && value.sold_at == null;
        },
        capitalize : function (string) {
            return string.replace(/^./, function (match) {
                return match.toUpperCase();
            });
        },
        openTaskForm: function(task, newTask){
            if (task) {
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
                    'name': null,
                    'date': App.formatDate(new Date),
                    'recurring': "1", 'type': "general", 'icon' : 'fa-cutlery bg-red',
                    relation: {id: ''}
                };
                if (newTask) {
                    this.activeTask = $.extend(this.activeTask, newTask);
                }
            }

            $('#new_task').modal('show');
        },
        createTask: function(mode) {
            var self = this;
            this.$validate(true);
            if (this.$activeTaskValidator.touched && this.$activeTaskValidator.invalid) return;
            this.loading = 1;
            if (mode == 'edit'){
                this.activeTask.type_id = this.activeTask.relation.id || null;
                this.activeTask.type_changed = (this.originalTask.type !== this.activeTask.type)
                                                || (this.originalTask.type_id !== this.activeTask.type_id);
                api.saveEvent(this.activeTask).then(() => {
                    if (self.activeTask.closed == '0') {
                        api.reopenEvent(self.activeTask);
                    } else {
                        api.closeEvent(self.activeTask);
                    }
                    this.$dispatch('litter-reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    self.loading = 0;
                    $('#new_task').modal('hide');
                });
            } else {
                this.activeTask.type_id = this.activeTask.relation ? this.activeTask.relation.id : null;
                api.saveEvent(this.activeTask).then(() => {
                    this.$dispatch('litter-reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    this.$dispatch('reload-tasks', {typeOfTask: this.type_of_task, page: this.page});
                    self.loading = 0;
                    $('#new_task').modal('hide');
                });
            }
        },

        finishTask: function (task) {
            if (task.closed == '1') {
                api.reopenEvent(task).then(() => {
                    task.closed = '0';
                });
            } else {
                api.closeEvent(task).then(() => {
                    task.closed = '1';
                });
            }
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
        loadBreeders: function(){
            this.loading = 1;
            api.getBreedersList().then(breeders => {
                this.loading = 0;
                this.breeder_litter = breeders.bucks.concat(breeders.does);
            });
        },
        loadLitters: function(){
            this.loading = 1;
            api.getLittersList().then(litters => {
                this.loading = 0;
                var tempRabbits = [];
                litters.forEach(function (item) {
                    var name = '';
                    if (item.parents[0]) {
                        name = item.parents[0].name
                    }
                    if (item.parents[1]) {
                        name = name + '/' + item.parents[1].name;
                    }
                    tempRabbits.push({'given_id' : item.given_id, 'id' : item.id, 'name' : name});
                });
                this.breeder_litter = tempRabbits;
            });
        },
        showRequestForm: function(task){
            this.activeTask = task;
            $('#delete_task').modal('show');
        }
    },

    mixins: [App.Mixins.BreedersFilter]
};
