App.Components.LitterProfile = {
    template: "#litter-profile-template",
    data: function () {
        return {
            litter: {
                id: this.$route.params.id,
                parents: []
            },
            litters: [],
            activeLitter: {},
            activeKit: {},
            activeKits: [],
            litterLoad: false,
            aliveKitsAmount: null,
            activeTimeline: [],
            plans: [],
            activeBirth: {},
            loadingImageMother: 0,
            loadingImageFather: 0,
            activeSex: null,

            // Cage card template.
            template: {},
            alltemplates: {},
            profileid: 0,


            newNote: null,
            noteIndexToEdit: {}
        }
    },
    props: [],
    computed: {
        confirmTarget: function () {
            return "this litter";
        },
        aliveKits: function () {
            return this.aliveKitsAmount != null ? this.aliveKitsAmount : ""; /*litter.kits_amount - litter.kits_died*/
        },
        breederNotes: function () {
            var arr = [];
            try {
                arr = JSON.parse(this.litter.notes);
            } catch (err) {
                if (this.litter.notes) {
                    var buf = this.litter.notes;
                    arr.push(buf);
                }
            }
            return arr;
        }
    },
    components: {
        'kit-form': App.Components.KitForm,
        'litter-box': App.Components.LitterBox,
        'litter-form': App.Components.LitterForm,
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher,
        'task-form' : App.Components.TaskForm,
        'ledger-table': App.Components.LedgerTable,
        // 'image-cropper-modal': App.Components.ImageCropper,
        'cage-cards-print': App.Components.CageCardsPrint,
    },
    events: {
        'litter-reload-tasks': function(msg){
            this.loadTasks('litters');
        },

        'litter-updated': function(msg){
            this.$broadcast('refresh-kits-global', this.litter.id);
        }
    },
    methods: {
        isEmptyTimeline: function(){
            return !this.activeTimeline[0].length
        },
        loadLitter: function () {
            api.getLitter(this.litter.id).then(res => {
                this.litter = res;
                if(res.butchered_at){
                    this.butchered = true;
                    this.litter.butchered_date = moment(this.litter.butchered_at, 'YYYY-MM-DD H:i:s')
                                                    .format(App.dateFormat);
                }
                this.litterLoad = true;
                this.initUploader();
            });
        },
        loadAllLitters: function () {
            api.getLitters().then(res => {
                this.litters = res.data;
            });
        },
        updateSettings: function () {

        },
        editKit: function (kit) {
            var self = this;
            this.activeKit = kit;
            var form = $('#kit-form-modal');
            form.modal({
                backdrop: 'static',
                keyboard: false
            });
            form.modal('show');
            $(document).one('hide.bs.modal', '#kit-form-modal', function(event){
                if($(event.target).is('#kit-form-modal')) {
                    self.refreshKits();
                }
            });
        },
        refreshKits: function () {
            console.log('REFRESH KITS');
            this.loadLitter();
            this.$broadcast('refresh-kits-global', this.litter.id);
        },

        father: function () {
            return _.find(this.litter.parents, function (item) { return item.sex == "buck" });
        },
        mother: function () {
            return _.find(this.litter.parents, function (item) { return item.sex == "doe" });
        },

        updateAlive: function (data) {
            this.aliveKitsAmount = data;
        },

        editModal: function () {
            var self = this;
            var form = $('#litter-form');
            form.modal({
                backdrop: 'static',
                keyboard: false
            })
            form.modal('show');
            $(document).one('hide.bs.modal', '#litter-form', function(event){
                if($(event.target).is('#litter-form')){
                    self.loadLitter();
                }
            });
        },

        weightModal: function () {
            this.activeLitter = _.extend({}, this.litter);
            api.getLitterKitsWeighOfType(this.litter.id, 'rabbitkit').then(kits => {
                this.activeKits = kits;
            });
            $('#litter-weight-modal').modal('show');
        },

        butcherModal: function (litter) {
            $('#litter-butcher-modal').modal('show');
        },

        archiveModal: function () {
            $('#archive-breed-modal').modal('show');
        },
        archive: function () {
            $('#archive-breed-modal').modal('hide');
            api.archiveLitter(this.litter).then(() => {
                this.$router.go({ path: '/litters' });
            });
        },

        deleteModal: function () {
            $('#delete-breed-modal').modal('show');
        },

        delete: function () {
            $('#delete-breed-modal').modal('hide');
            api.deleteLitter(this.litter).then(() => {
                this.$router.go({ path: '/litters' });
            });
        },

        loadRabbit: function(rabbit, type){
            this.activeRabbit = rabbit;
            this.loadTasks(type);
        },

        loadTasks: function(type){
            api.getTypeEvents(type == 'general' ? 'users' : type, this.activeRabbit.id).then(data => {
                this.activeTimeline = [];
                this.activeTimeline.push(data);
            });
        },

        getTimeLeft: function(task){
            var oToday = new Date();
            var oDeadLineDate = new Date(task.date);
            var nDaysLeft = oDeadLineDate > oToday ? Math.ceil((oDeadLineDate - oToday) / (1000 * 60 * 60 * 24)) : null;
            if(!nDaysLeft) return 'Today';

            if(nDaysLeft == 1) return nDaysLeft + ' day';
            return nDaysLeft + ' days';
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

        openTaskForm: function (task) {
            this.activeTask = task;
            this.$broadcast('tasks-managing', task);
        },

        openOwnerTaskForm: function (task) {
            var newTask = {};
            newTask.type = 'litter';
            newTask.relation = {};
            newTask.relation.id = this.litter.id;
            this.$broadcast('tasks-managing', task, newTask);
        },

        showRequestForm: function(task){
            this.activeTask = task;
            $('#delete_task').modal('show');
        },

        deleteTask : function(task){
            if (task && task.id) {
                api.deleteEvent(task).then(() => {
                    $('#id_task_'+task.id).slideUp(200);
                    $("#delete_task").modal('hide');
                    this.activeTask = {};
                    this.$dispatch('litter-reload-tasks');
                });
            } else {
                var tasksForDelete = [];
                this.activeTasks.forEach(function (item) {
                    if(item.closed == 1) tasksForDelete.push(item.id);
                });

                if (tasksForDelete.length) {
                    api.archiveEvents(tasksForDelete).then(() => {
                        $("#delete_task").modal('hide');
                        this.$dispatch('litter-reload-tasks');
                    });
                } else {
                    $("#delete_task").modal('hide');
                }
            }
        },
        loadLedger: function () {
            this.$broadcast('load_ledger');
        },

        // Image resize/cropper
        // =====================================
        imageCropperModal: function(input) {
            var self = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#image-cropper-modal .image-cropper-container').attr('src', e.target.result);
                    $('#image-cropper-modal').modal('show');
                    $('#image-cropper-modal .image-cropper-container').cropper({
                        aspectRatio: 1,
                        autoCropArea: 0.3
                    });
                }
                reader.readAsDataURL(input.files[0]);
                $(document).one('hide.bs.modal', '#image-cropper-modal', function(event){
                    if($(event.target).is('#image-cropper-modal')){
                        $('#image-cropper-modal .image-cropper-container').cropper('destroy');
                        $(input).val('');
                    }
                });
            }
        },
        uploadImage: function() {
            var self = this;
            if (this.activeSex === 'father') {
                this.loadingImageFather = 1;
            } else {
                this.loadingImageMother = 1;
            }
            $('#image-cropper-modal .image-cropper-container').cropper('getCroppedCanvas').toBlob(function(blob) {
                $('#image-cropper-modal').modal('hide');
                var formData = new FormData();
                formData.append('file', blob);
                formData.append('upload_preset', App.cloud_preset);
                var cloud_upload_url = App.cloud_api_base_url + '/image/upload';
                $.ajax({
                    url: cloud_upload_url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(res) {
                    var breeder = null;
                    if (self.activeSex === 'father') {
                        breeder = self.father();
                    } else {
                        breeder = self.mother();
                    }
                    breeder.image.name = res.public_id;
                    breeder.image.path = res.url;
                    breeder.image.temp = true;
                    self.sendBreeder(breeder);
                }).fail(function(err) {
                    console.log('Upload error');
                    console.log(err);
                }).complete(function() {
                    self.loadingImageFather = 0;
                    self.loadingImageMother = 0;
                    self.initUploader();
                });
            });
        },

        initUploader: function () {
            var self = this;
            this.$nextTick(function(){
                $(this.$els['imagefather']).on('change', function(event) {
                    self.activeSex = 'father';
                    self.imageCropperModal(this);
                })
                $(this.$els['imagemother']).on('change', function(event) {
                    self.activeSex = 'mother';
                    self.imageCropperModal(this);
                })
            });
        },
        // =====================================
        
        uploaderHelperFather: function () {
            $(this.$els['imagefather']).click();
        },

        uploaderHelperMother: function () {
            $(this.$els['imagemother']).click();
        },

        sendBreeder: function (breeder) {
            if(breeder.id != 0) {
                breeder._method = "PUT";
            }

            this.$http.put('/admin/breeders/' + breeder.id + '/image', breeder).then(function (response) {
                var breederResponse = response.data;
                if (breeder.id == 0) {
                    this.breeders.push(breederResponse);
                } else {
                    var match = _.find(this.breeders, function (item) {
                        return item.id === breeder.id
                    });
                    if (match) {
                        _.extendOwn(match, breederResponse)
                    }
                    this.breeder = breederResponse;
                }
            }.bind(this), function (response) {
                this.errors = response.data;
            });

        },

        /**
         * Cage card.
         */
        cageCard: function(){
            var request = {type: 'litter'};
            this.template = {};

            api.getCageCardsTemplates(request).then(data => {
                this.alltemplates = data.templates;
            });

            this.profileid = this.litter.id;

            $('#cage-cards-print-modal').modal('show');

        },


        // function for notes
        updateNotes: function () {

            var litter = this.litter;

            api.saveLitter(litter).then(
                data => {
                    if (litter.id == 0) {
                        data.archived = 0;
                        this.litters.push(data);
                    } else {
                        this.litter = data;
                        this.litter.butchered_date = moment(this.litter.butchered_at, 'YYYY-MM-DD H:i:s')
                            .format(App.dateFormat);
                    }
                    this.$dispatch('litter-updated', this.litter.id);
                    this.newNote = "";
                },
                response => {
                    this.loading = 0;
                    this.errors = response.errors;
                    this.newNote = "";
                }
            );
        },
        addNote: function () {
            var notesArr = [];
            try {
                if (this.litter.notes) {
                    notesArr = JSON.parse(this.litter.notes);
                }
                else {
                    notesArr = [];
                }
            } catch (err) {
                var buf = this.litter.notes;
                notesArr = [];
                notesArr.push(buf);
            }
            console.log('tuts');
            console.log(notesArr);
            notesArr.push(this.newNote);
            this.litter.notes = JSON.stringify(notesArr);
            this.updateNotes();
        },
        editNote: function () {
            var notesArr = [];

            try {
                if (this.litter.notes) {
                    notesArr = JSON.parse(this.litter.notes);
                }
                else {
                    notesArr = [];
                }
            } catch (err) {
                var buf = this.litter.notes;
                notesArr = [];
                notesArr.push(buf);
            }

            for (var i = 0; i < notesArr.length; i++) {
                if (i === this.noteIndexToEdit.num) {
                    notesArr[i] = this.noteIndexToEdit.not;
                    break;
                }
            }

            this.litter.notes = JSON.stringify(notesArr);
            this.updateNotes();
            this.noteIndexToEdit = {
                index: -1,
                note: ''
            };
        },
        deleteNote: function (index) {
            var notesArr = [];
            try {
                if (this.litter.notes) {
                    notesArr = JSON.parse(this.litter.notes);
                }
            } catch (err) {
                var buf = this.litter.notes;
                notesArr = [];
                notesArr.push(buf);
            }

            var newArr = [];

            for (var i = 0; i < notesArr.length; i++)
                if (i !== index)
                    newArr.push(notesArr[i]);

            this.litter.notes = JSON.stringify(newArr);

            this.updateNotes();
        },
        setNote: function (note, index) {
            this.noteIndexToEdit = {
                not: note,
                num: index
            };
            console.log('hueta: ',this.noteIndexToEdit);
        }
    },
    ready: function () {
        App.MobileTypes();
        this.loadLitter();
        this.loadAllLitters();
        this.birthModal();

        var action = localStorage.getItem('action');
        if (action === "edit")
            this.editModal();
        else if (action === "cageCard")
            this.cageCard();

        localStorage.removeItem('action');

    },
    mixins: [App.Mixins.Butcherable]
};
