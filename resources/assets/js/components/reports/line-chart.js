App.Components.LineChart = {

    twoWay: true,

    template:
    '<canvas width="{{ width }}" height="{{ height }}" v-el:canvas></canvas>',
    data: function () {
        return {
            ctx: null,
            chart: null
        }
    },

    props: ['type', 'width', 'height', 'data', 'options', 'datasets', 'labels', 'legend'],
    computed: {

    },
    components: {

    },
    watch: {

    },
    events: {
        'show-chart': function(){
            if (this.datasets && this.labels && this.type){

                if (Chart.defaults.global.colours) {
                    if(this.type == 'pie' || this.type == 'doughnut'){
                        _.each(this.datasets, function (dataset, index) {
                            if (dataset.backgroundColor === undefined) {
                                //_.extend(dataset, Chart.defaults.global.colours);
                                dataset.backgroundColor = [];
                                _.each(Chart.defaults.global.colours, function (color) {
                                    dataset.backgroundColor.push(color.borderColor);
                                })
                            }
                        });
                    } else {
                        _.each(this.datasets, function (dataset, index) {
                            if (dataset.borderColor === undefined) {
                                _.extend(dataset, Chart.defaults.global.colours[index % Chart.defaults.global.colours.length]);
                            }
                        });
                    }
                }

                this.chart = new Chart(this.ctx, {
                    type: this.type,
                    data: {
                        labels: JSON.parse(JSON.stringify(this.labels)),
                        datasets: JSON.parse(JSON.stringify(this.datasets))
                    },
                    options: this.options
                });
                if(this.legend){
                    $(this.legend).html(this.chart.generateLegend());
                }
            }
        }
    },
    methods: {

    },
    ready: function () {
        this.ctx = $(this.$els.canvas).get(0).getContext("2d");
        this.$watch('datasets', function (newVal, oldVal) {
            if(this.chart){
                this.chart.destroy();
                this.$emit('show-chart');

            }
        })
    }
};
