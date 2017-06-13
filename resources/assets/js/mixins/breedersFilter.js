if (!App.Mixins) {
    App.Mixins = {};
}

App.Mixins.BreedersFilter = {
    data: function() {
        return {
            withArchivedFather: false,
            withArchivedMother: false,
            withArchivedBreeders: false,
            //old
            mother_id: null,
            father_id: null,
            //old end
            entry_id: null,

            //new
            // father_name: null,
            // mother_name: null,
            //
            // local_buck: [],
            // local_does: []
        };
    },
    watch: {
        // mother_id: function () {
        //     for (var i = 0; i < this.local_does.length; i++) {
        //         console.log('mother: ', this.mother_id);
        //         console.log('id: ', this.local_does[i].id);
        //         console.log(parseInt(this.mother_id) === parseInt(this.local_does[i].id));
        //         if (parseInt(this.mother_id) === parseInt(this.local_does[i].id))
        //             this.mother_name = this.local_does[i].name;
        //     }
        //     console.log('mother: ', this.mother_name);
        // },
        // father_id: function () {
        //     for (var i = 0; i < this.local_buck.length; i++) {
        //         console.log('bucks: ', this.local_buck[i].name);
        //         if (parseInt(this.father_id) === parseInt(this.local_buck[i].id))
        //             this.father_name = this.local_buck[i].name;
        //     }
        //     console.log('mother: ', this.father_name);
        // }
    },

    methods: {
        //old
        setFilterBreeders(mother_id, father_id){
            this.father_id = father_id;
            this.mother_id = mother_id;
        },
        //new
        // setFilterBreedersNames(does, bucks){
        //     console.log('does: ', does);
        //     console.log('bucks: ', bucks);
        //     this.local_does = does;
        //     this.local_buck = bucks;
        // },
        setFilterBreedersEntry(id){
            this.entry_id = id;
        },

        filterFatherBreeders: function (value) {
            if (this.withArchivedFather) {
                return true;
            }
            return this.father_id == value.id || value.archived === 0  && value.butchered === 0 && value.died === 0  && value.sold_at == null;
        },

        filterMotherBreeders: function (value) {
            if (this.withArchivedMother) {
                return true;
            }
            return this.mother_id == value.id || value.archived === 0  && value.butchered === 0 && value.died === 0 && value.sold_at == null;
        },

        resetArchivedFilterBreeders: function () {
            this.withArchivedMother = false;
            this.withArchivedFather = false;
            this.withArchivedBreeders = false;
        }
    }
};
