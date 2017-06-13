App.Components.BirthForm = App.Components.Exploitable.Section.extend({
    template: "#birth-form-template",
    data: function () {
        return {
            modalUnique: 'plan',
            errors: {},
            doe: {}
        }
    },
    props: ['plans', 'activeBirth'],
    watch: {
        'activeBirth.breedplan': function (val) {
            console.log(val);
            if(typeof val == 'string' && val != -1){
                api.getPlanNextLitterId(val).then(response => {
                    this.$set('activeBirth.given_id', response.nextLitterId);
                    this.doe = response.doe;
                })
            } else {
                this.$set('activeBirth.given_id', '');
            }
        }
    },
    components: {},
    computed: {},
    events: {
        doMissedModel: function(plan) {
            api.missBirth(this.activeBirth).then(() => {
                $('#missing-modal-birth-plan').modal('hide');
                $('#birth-form-modal').modal('hide');
                $('#birth-schedule-next-modal').modal('show');
                this.$dispatch('reload-tasks', {typeOfTask: 'all', page: null});
            });
        },
        'new-birth-modal-open': function(){
            this.errors = {};
        }
    },
    methods: {
        recordBirth: function () {
            api.recordBirth(this.activeBirth).then(
                () => {
                    $('#birth-form-modal').modal('hide');
                    $('#birth-schedule-next-modal').modal('show');
                    this.$dispatch('reload-tasks', {typeOfTask: 'all', page: null});
                },
                response => {
                    this.errors = response.data;
                }
            );
        },
        showMissed: function() {
            $('#missing-modal-birth-plan').modal('show');
        },

        scheduleNextBreed: function (doeId) {
            $('#birth-schedule-next-modal').modal('hide');
            this.$dispatch('open-breed-modal', doeId);
        }
    },
    ready: function () {
        //this.scheduleNextBreed(doe.id);
        //$('#breed-edit').modal('show')
    }

});
