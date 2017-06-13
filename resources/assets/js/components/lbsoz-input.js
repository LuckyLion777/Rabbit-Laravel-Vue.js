App.Components.LbsOzInput = {
    twoWay: true,
    deep: true,
    template:
    '<div>'+
        '<div class="input-group">'+
            '<input type="number" min="0" pattern="\d*" class="form-control lbs" v-model="lbs" debounce="20000">'+
            '<span class="input-group-btn" style="width:0px;"></span>'+
            '<input type="number" min="0" class="form-control oz" v-model="oz"  debounce="20000">'+
        '</div>'+
    '</div>',

    data: function () {
        return {
            localWeight: 0
        }
    },
    props: ['weight', 'model', 'reset'],

    computed: {
        lbs: {
            get: function(){
                return (this.weight? Math.floor(this.weight / 16) : null );
            },
            set: function(newValue){
                this.weight = newValue * 16 + (this.oz? parseFloat(this.oz): 0);
            }
        },

        oz: {
            get: function(){
                if(this.weight){
                    var oz = parseFloat((this.weight - this.lbs * 16).toFixed(3))
                    if(oz != 0){
                        return oz;
                    }
                }
                return null;
            },
            set: function(newValue){
                    this.weight = this.lbs * 16 + (newValue? parseFloat(newValue) : 0);
            }
        }
    },
    components: {},
    watch: {
        model: function(){
            this.localWeight = this.weight;
        }
    }
    ,
    events: {},
    methods: {},

    ready: function () {
        if(this.reset){
            this.weight = '';
        }
    }
};
