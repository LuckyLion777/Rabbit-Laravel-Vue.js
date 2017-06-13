App.Components.Pedigree = {
    template: "#pedigree-template",
    data: function () {
        return {
            breeder: {
                image: {}
            },
            generations: {},
            loadingPedigree: 1,
            token: ''
        }
    },
    components: {
        'pedigree-form': App.Components.PedigreeForm,
        'copy-pedigree-form': App.Components.CopyPedigreeForm
    },
    props: ["id", "token"],
    computed: {
        breedSex: function () {
            if(this.breeder.sex == "doe") {
                return "Doe";
            } else if(this.breeder.sex == "buck") {
                return "Buck";
            } else {
                return "Breed";
            }
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
        }
    },
    methods: {

        showProfile: function (id, callback) {
            api.getPedigree(id).then(data => {
                this.breeder = data;
                if (callback != undefined) {
                    callback();
                }
            });
        },
        loadLitters: function () {
            this.loadingPedigree = 1;
            api.getBreederPedigree(this.id).then(data => {
                this.generations = data;
                this.loadingPedigree = 0;
            });
        },

        edit: function (id) {
            this.showProfile(id, this.showModal);
        },

        showModal() {
            $('#pedigree-form').modal('show');
        },
        
        showCopy() {
            $('#copy-pedigree-form').modal('show');
        }

    },
    events: {
        load_pedigree: function () {
            var gthis = this;
            gthis.loadLitters();
            $('#pedigree-form').on('hidden.bs.modal', function (e) {
                // do something...
                gthis.loadLitters();
            })
        }
    }

};
