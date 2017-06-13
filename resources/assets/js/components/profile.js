App.Components.Profile = App.Components.Exploitable.Section.extend({
    template: "#profile-template",
    data: function () {
        return {
            breeder: {
                image: {}
            },
            breeders: [],
            bucks: [],
            does: [],
            errors: {},
            litters: [],
            activeKits: [],
            activeKit: {},
            activeLitter: {},
            activeTimeline:[],
            activeTask: {},
            loadingLitters: 0,
            loadingTasks: 0,
            loading: 0,
            soldValue: 0,
            transferTo: '',
            transferErrors: {},
            // Cage card template.
            template: {},
            alltemplates: {},
            profileid: 0,
            confirmTarget: 'breeder',

            newNote: null,
            noteIndexToEdit: {},

            litterDrop: false
        }
    },
    props: [],
    components: {
        'kit-form': App.Components.KitForm,
        'litter-box': App.Components.LitterBox,
        'breeder-form': App.Components.BreederForm,
        'litter-weight': App.Components.LitterWeight,
        'litter-butcher': App.Components.LitterButcher,
        'breeder-butcher': App.Components.BreederButcher,
        'litter-form': App.Components.LitterForm,
        'task-form' : App.Components.TaskForm,
        'breeder-pedigree': App.Components.Pedigree,
        "ledger-table": App.Components.LedgerTable,
        'cage-cards-print': App.Components.CageCardsPrint
    },
    computed: {
        id: function () {
            return this.$route.params.id;
        },
        breedSex: function () {
            this.getRabbitSex(this.breeder);
        },
        breedName: function () {
            return this.breeder.name;
        },
        breedSexClass: function () {
            if(this.breeder.sex == "doe") {
                return "box-danger";
            } else if(this.breeder.sex == "buck") {
                return "box-info";
            }
        },
        confirmTarget: function () {
            return this.breeder.name;
        },
        breederNotes: function () {
            var arr = [];
            try {
                arr = JSON.parse(this.breeder.notes);
            } catch (err) {
                if (this.breeder.notes) {
                    var buf = this.breeder.notes;
                    arr.push(buf);
                }
            }
            return arr;
        }
    },
    // watch: {
    //     noteIndexToEdit: function () {
    //         console.log(this.noteIndexToEdit);
    //     }
    // },
    events: {
        'reload-tasks': function(msg) {
            this.loadTasks('breeders');
        },
        doMissedModel: function() {
            var plan_id = this.breeder.plan_id;
            var activeBirth = {};
            activeBirth = {breedplan: plan_id || '-1', born: moment(new Date()).format(App.dateFormat)};
            if(typeof plan_id == 'string' && plan_id != -1){
                api.getPlanNextLitterId(plan_id).then(response => {
                    activeBirth.given_id = response.nextLitterId;
                    this.missBirth(activeBirth);
                })
            } else {
                activeBirth.given_id = '';
                this.missBirth(activeBirth);
            }
        },
    },
    methods: {
        missBirth: function(activeBirth) {
            var self = this;
            api.missBirth(activeBirth).then(() => {
                $('#missing-modal-birth-plan').modal('hide');
                console.log('showProfile');
                self.showProfile();
            });
        },
        isEmptyTimeline: function(){
            return !this.activeTimeline[0].length
        },
        showProfile: function () {
            api.getBreeder(this.id).then(data => {
                this.breeder = data;
                console.log('show profile');
                console.log(this.breeder);
                console.log('show profile end');
            });
        },
        edit: function () {
            var self = this;
            var form = $('#breeder-form');
            form.modal({
                backdrop: 'static',
                keyboard: false
            });
            form.modal('show');
            $(document).on('hide.bs.modal', '#breeder-form', function(event){
                if($(event.target).is('#breeder-form')) {
                    self.showProfile();
                }
            });
            api.getBreedersList().then(breeders => {
                this.bucks = breeders.bucks;
                this.does = breeders.does;
            });
        },
        getParent: function (parent) {
            return parent ? parent.name + ": " + parent.tattoo : "Unknown";
        },

        soldModal: function(id) {
          $('#sold-breed-modal').modal('show');
        },
        sold: function() {
            $('#sold-breed-modal').modal('hide');
            api.sellBreeder(this.breeder, this.soldValue).then(() => {
                this.soldValue = 0;
            });
            $('#transfer-breed-modal').modal('show').on('hide.bs.modal', function () {
                this.$router.go('/breeders');
            }.bind(this));
        },

        transfer: function() {
            api.transferBreeder(this.breeder, this.transferTo).then(
                () => {
                    $('#transfer-breed-modal').modal('hide');
                    this.transferTo = '';
                    this.transferErrors = {};
                },
                response => { this.transferErrors = response.data; }
            );
        },

        archiveModal: function (id) {
            $('#archive-breed-modal').modal('show');
        },
        archive: function () {
            $('#archive-breed-modal').modal('hide');
            api.archiveBreeder(this.breeder).then(() => {
                this.$router.go({ path: '/breeders/archive' });
            });
        },

        deleteModal: function (id) {
            $('#delete-breed-modal').modal('show');
        },
        delete: function () {
            $('#delete-breed-modal').modal('hide');
            api.deleteBreeder(this.breeder).then(() => {
                this.$router.go({ path: '/breeders' });
            });
        },


        loadLitters: function () {
            this.loadingLitters = 1;
            api.getBreederLitters(this.id).then(litters => {
                this.litters = litters.data;
                this.loadingLitters = 0;
            });
        },

        loadPedigree: function () {
            this.$broadcast('load_pedigree');
        },

        loadLedger: function () {
            this.$broadcast('load_ledger');
        },

        weightLitter: function (litter) {
            this.activeLitter = litter;
            api.getLitterKitsWeighOfType(this.activeLitter.id, 'rabbitkit').then(kits => {
                this.activeKits = kits;
            });
            $('#litter-weight-modal').modal('show');
        },

        butcherModal: function (litter) {
            this.activeLitter = litter;
            api.getLitterKitsWeighOfType(this.activeLitter.id, 'rabbitkit').then(kits => {
                this.activeKits = kits;
            });
            $('#litter-butcher-modal').modal('show');
        },

        editLitterModal: function (litter) {
            this.activeLitter = litter;
            $('#litter-form').modal('show');
        },

        archiveLitterModal: function (litter) {
            this.activelitter = litter;
            $('#archive-litter-modal').modal('show');
        },
        archiveLitter: function () {
            $('#archive-litter-modal').modal('hide');
            api.archiveLitter(this.activelitter).then(() => {
                $('#id_' + this.activelitter.id).slideUp(200);
                this.refreshKits();
            });
        },

        deleteLitterModal: function (litter) {
            this.activelitter = litter;
            $('#delete-litter-modal').modal('show');
        },

        showMissed: function() {
            $('#missing-modal-birth-plan').modal('show');
        },

        deleteLitter: function () {
            $('#delete-litter-modal').modal('hide');
            api.deleteLitter(this.activelitter).then(() => {
                $('#id_' + this.activelitter.id).slideUp(200);
                this.refreshKits();
            });
        },

        openTaskForm: function (task) {
            this.activeTask = task;
            this.$broadcast('tasks-managing', task);
        },

        openOwnerTaskForm: function (task) {
            var newTask = {};
            newTask.type = 'breeder';
            newTask.relation = {};
            newTask.relation.id = this.breeder.id;
            this.$broadcast('tasks-managing', task, newTask);
        },

        showRequestForm: function(task){
            this.activeTask = task;
            $('#delete_task').modal('show');
        },

        deleteTask : function(task) {
            if (task && task.id) {
                api.deleteEvent(task).then(() => {
                    $('#id_task_'+task.id).slideUp(200);
                    $("#delete_task").modal('hide');
                    this.activeTask = {};
                    this.$dispatch('reload-tasks');
                });
            } else {
                var tasksForDelete = [];
                this.activeTasks.forEach(function (item, i, arr) {
                    if(item.closed == 1) tasksForDelete.push(item.id);
                });

                if (tasksForDelete.length) {
                    api.archiveEvents(tasksForDelete).then(() => {
                        $("#delete_task").modal('hide');
                        this.$dispatch('reload-tasks');
                    });
                } else {
                    $("#delete_task").modal('hide');
                }
            }
        },

        editKit: function (kit) {
            this.activeKit = kit;
            $('#kit-form-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#kit-form-modal').modal('show');
        },
        refreshKits: function () {
            this.loadLitters();
            this.showProfile();
        },

        makeDate: function (date) {
            return App.formatDate(date);
        },

        loadRabbit: function(rabbit, type){
            this.activeRabbit = rabbit;
            this.loadTasks(type);
        },

        loadTasks: function(type){
            this.loadingTasks = 1;

            var ntype = type == 'general' ? 'users' : type;

            api.getTypeEvents(ntype, this.activeRabbit.id).then(data => {
                this.activeTimeline = [];
                this.activeTimeline.push(data);
                this.loadingTasks = 0;
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

        getSecondParentSex: function(litter){
            var parent = this.getBreederPartner(litter);
            return this.getRabbitSex(parent);
        },

        getSecondParentName: function(litter){
            var parent = this.getBreederPartner(litter);
            return parent.name;
        },

        getBreederPartner: function(litter){
            var self = this;
            var parent = _.find(litter.parents, function(parent){
                return self.breeder.id != parent.id;
            });
            return parent;
        },
        getRabbitSex: function (rabbit) {
            if(rabbit.sex == "doe") {
                return "Doe";
            } else if(rabbit.sex == "buck") {
                return "Buck";
            } else {
                return "Breed";
            }
        },


        // Image resize/cropper
        // =====================================
        imageCropperModal: function(input) {
            var self = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#profile-image-cropper-modal .image-cropper-container').attr('src', e.target.result);
                    $('#profile-image-cropper-modal').modal('show');
                    $('#profile-image-cropper-modal .image-cropper-container').cropper({
                        aspectRatio: 1,
                        autoCropArea: 0.3
                    });
                }
                reader.readAsDataURL(input.files[0]);
                $(document).one('hide.bs.modal', '#profile-image-cropper-modal', function(event){
                    if($(event.target).is('#profile-image-cropper-modal')){
                        $('#profile-image-cropper-modal .image-cropper-container').cropper('destroy');
                        $(input).val('');
                    }
                });
            }
        },
        uploadImage: function() {
            var self = this;
            self.loading = 1;
            $('#profile-image-cropper-modal .image-cropper-container').cropper('getCroppedCanvas').toBlob(function(blob) {
                $('#profile-image-cropper-modal').modal('hide');
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
                    self.breeder.image.name = res.public_id;
                    self.breeder.image.path = res.url;
                    self.breeder.image.temp = true;
                    self.sendBreeder();
                }).fail(function(err) {
                    console.log('Upload error');
                    console.log(err);
                }).complete(function() {
                    self.loading = 0;
                    self.initUploader();
                });
            });
        },

        initUploader: function () {
            var self = this;
            $(this.$els['image']).on('change', function(event) {
                console.log('input change');
                self.imageCropperModal(this);
            })
        },
        // =====================================

        // initUploader: function () {
        //     var self = this;

        //     $(this.$els['image']).unsigned_cloudinary_upload("upload_images",
        //         { cloud_name: App.cloud_name},
        //         { multiple: false }
        //     ).bind('fileuploadstart', function(e, data) {

        //         self.loading = 1;
        //     }).bind('cloudinarydone', function(e, data) {
        //         self.loading = 0;
        //         self.breeder.image.name = data.result.public_id;
        //         self.breeder.image.path = data.result.url;
        //         self.breeder.image.temp = true;
        //         self.sendBreeder();
        //         self.initUploader();
        //     }).bind('fileuploadfail', function(e, data) {
        //         self.loading = 0;
        //         self.initUploader();
        //     });

        // },
        uploaderHelper: function () {
            $(this.$els['image']).click();
        },

        sendBreeder: function () {
            var breeder = this.breeder;

            if(breeder.id != 0) {
                breeder._method = "PUT";
            }

            this.$http.post('/admin/breeders/' + this.breeder.id + '/image', breeder).then(function (response) {
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

        showAge: function(breeder) {
            const date_of_birth = breeder.date_of_birth;

            if (!date_of_birth) {
                return '-'
            }
            const years = App.roundTo(moment().diff(App.parseMoment(date_of_birth), 'years', true), 2);
            if(years < 1){
                const months = App.roundTo(moment().diff(App.parseMoment(date_of_birth), 'months', true), 2);
                if(months === 1){
                    return '1 month'
                } else {
                    return months  + ' months';
                }
            }
            return years === 1? '1 year': years + ' years';
        },

        butcherBreederModal: function () {
            $('#breeder-butcher-modal').modal('show');
        },

        undoButcher: function(){

            var data = {
                breeders: [this.breeder],
                butchered: 0
            };

            api.butcherBreeder(this.breeder, data).then(() => {
                this.breeder.butchered = 0;
            });
        },

        showDied: function(){
            $('#died-modal-breeder').modal('show');
        },

        died: function() {
            var data = {
                died: 1
            };
            api.diedBreeder(this.breeder, data).then(() => {
                $('#died-modal-breeder').modal('hide');
                this.breeder.died = true;
                this.$dispatch('breederDied', this.breeder.id);
            });
        },
        undoDied: function(){

        },

        /**
         * Cage card.
         */
        cageCard: function(){
            var request = {type: 'breeder'};
            this.template = {};

            api.getCageCardsTemplates(request).then(data => {
                this.alltemplates = data.templates;
            });

            this.profileid = this.breeder.id;

            $('#cage-cards-print-modal').modal('show');

        },



        // function for notes
        updateNotes: function () {

            var breeder = this.breeder;

            api.saveBreeder(breeder).then(
                data => {
                    this.loading = 0;
                    App.vent.trigger('breederSaved');
                    if (breeder.id == 0) {
                        this.breeders.push(data);
                    } else {
                        var match = _.find(this.breeders, function (item) {
                            return item.id === breeder.id
                        });
                        if (match) {
                            _.extendOwn(match, data)
                        }
                        this.breeder = data;
                    }
                    this.newNote = "";
                },
                response => {
                    this.loading = 0;
                    if (response.data === 'breeders-limit-exceeded') {
                        $('#breeder-limit-alert').modal('show');
                    }
                    this.errors = response.data;
                }
            );

        },
        addNote: function () {
            var notesArr = [];
            try {
                if (this.breeder.notes) {
                    notesArr = JSON.parse(this.breeder.notes);
                }
                else {
                    notesArr = [];
                }
            } catch (err) {
                var buf = this.breeder.notes;
                notesArr = [];
                notesArr.push(buf);
            }
            console.log('tuts');
            console.log(notesArr);
            notesArr.push(this.newNote);
            this.breeder.notes = JSON.stringify(notesArr);
            this.updateNotes();
        },
        editNote: function () {
            var notesArr = [];

            try {
                if (this.breeder.notes) {
                    notesArr = JSON.parse(this.breeder.notes);
                }
                else {
                    notesArr = [];
                }
            } catch (err) {
                var buf = this.breeder.notes;
                notesArr = [];
                notesArr.push(buf);
            }

            for (var i = 0; i < notesArr.length; i++) {
                if (i === this.noteIndexToEdit.num) {
                    notesArr[i] = this.noteIndexToEdit.not;
                    break;
                }
            }

            this.breeder.notes = JSON.stringify(notesArr);
            this.updateNotes();
            this.noteIndexToEdit = {
                index: -1,
                note: ''
            };
        },
        deleteNote: function (index) {
            var notesArr = [];
            try {
                if (this.breeder.notes) {
                    notesArr = JSON.parse(this.breeder.notes);
                }
            } catch (err) {
                var buf = this.breeder.notes;
                notesArr = [];
                notesArr.push(buf);
            }

            var newArr = [];

            for (var i = 0; i < notesArr.length; i++)
                if (i !== index)
                    newArr.push(notesArr[i]);

            this.breeder.notes = JSON.stringify(newArr);

            this.updateNotes();
        },
        setNote: function (note, index) {
            this.noteIndexToEdit = {
                not: note,
                num: index
            };
            console.log('hueta: ',this.noteIndexToEdit);
        },
        openLitter: function (item, id) {
            localStorage.setItem('action', item);
            this.$route.router.go('/litterprofile/' + id);
        }
    },
    ready: function () {
        this.showProfile();
        this.loadLitters();
        App.MobileTypes();
        this.initUploader();
        this.$on('refresh-kits', this.refreshKits.bind(this));

        var action = localStorage.getItem('action');
        if (action === "cageCard")
            this.cageCard();
        else if (action === "butcher")
            this.butcherBreederModal();
        else if (action === "undoButcher")
            this.undoButcher();
        else if (action === "died")
            this.showDied();
        else if (action === "undoDied")
            this.undoDied();

        localStorage.removeItem('action');

    },
    mixins: [App.Mixins.Butcherable, App.Mixins.DeathReason]
});
