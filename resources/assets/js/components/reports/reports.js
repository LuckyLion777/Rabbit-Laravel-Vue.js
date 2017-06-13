App.Components.Reports = {
    template: "#reports-template",
    data: function () {
        return {
            production:{
              littersPerDoe: 4
            },
            meatProduction: {
              limit: 9,
              otherColor: "#CCC"
            },
            loadingStatics: 1,
            loadingDoes: 1,
            loadingBucks:1,
            weightSlug: "",
            statistics: {},
            does: [],
            topMeatdoes: [],
            activeDoes: [],
            showReasonForDeathChart: false,
            report_date_range: {
                from: null,
                to: null
            },
            datasets: {
                production: [],
                meatProduction: [],
                butcherAge: [],
                suirvivalRate: [],
                liveAndDied:[],
                reasonsForDeath: [],
                missedBreedings: [],
                missedBucksBreedings: []
            },
            labels: {
                production: [],
                meatProduction: [],
                butcherAge: [],
                suirvivalRate: [],
                liveAndDied: [],
                reasonsForDeath: [],
                missedBreedings: [],
                missedBucksBreedings: []
            },
            options: {
                production:{
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            title: function(){},
                            label: function(tooltipItem, data) {

                                return this.showWeights(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index])

                            }.bind(this)
                        }
                    },
                    legend : {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                min: 0,
                                callback: function(value, index, values) {
                                    if(this.weightSlug == "" || this.weightSlug == "Pounds"){
                                        return (value % 32 == 0? value/16: null);
                                    }
                                    return Math.round(value * 100) / 100
                                }.bind(this)
                            }
                        }]
                    }
                },
                butcherAge: {
                    legend : {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                },
                missedBreedings: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                stepSize: 1
                            }
                        }]
                    }
                },
                missedBucksBreedings: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                stepSize: 1
                            }
                        }]
                    }
                },
                meatProduction: {
                    legend: {
                        display: false
                    },
                    legendCallback: function(chart){
                        var text = [];
                        text.push('<ul class="' + chart.id + '-legend">');

                        if (chart.data.datasets.length) {
                            for (var i = 0; i < chart.data.datasets[0].data.length; ++i) {
                                text.push('<li><i class="fa fa-circle-o text-' + this.colours[i % 10] +' }}"></i> ');
                                if (chart.data.labels[i]) {
                                    text.push(chart.data.labels[i]);
                                    text.push(' - ' + this.showWeights(chart.data.datasets[0].data[i]));
                                }
                                text.push('</li>');
                            }
                        }

                        text.push('</ul>');
                        return text.join("");
                    }.bind(this)
                },

                reasonsForDeath: {
                    legend : {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                min: 0,
                                // max: 4
                            }
                        }]
                    }
                },
            },
            colours: [
                'red',
                'green',
                'yellow',
                'aqua',
                'light-blue',
                'teal',
                'purple',
                'maroon',
                'orange',
                'black'
            ]
        }
    },
    props: [],
    computed: {

    },
    components: {
        'line-chart': App.Components.LineChart
    },
    events: {
        'load-statistics': function () {
            this.loadingStatics = 1;
            var req_data = {};
            if (this.report_date_range[0] && this.report_date_range[1]) {
                req_data = this.setDateFromDateRangePicker(this.report_date_range);
            }
            api.getReportsStatistics(req_data).then(data => {
                console.log(data);
                this.loadingStatics = 0;
                this.statistics = data.statistics;
                this.weightSlug = data.weightSlug;
                this.showReasonsForDeath();
            });
        },
        'load-does': function(){
            this.loadingDoes = 1;
            var req_data = {};
            if (this.report_date_range[0] && this.report_date_range[1]) {
                req_data = this.setDateFromDateRangePicker(this.report_date_range);
            }
            api.getReportsDoes(req_data).then(data => {
                console.log(data);
                this.loadingDoes = 0;
                this.does = data.topWeightDoes.filter(doe => doe.litters && doe.litters.length);
                this.topMeatdoes = data.topMeatDoes;
                this.activeDoes = this.does;

                this.calculateMeatProduction(this.topMeatdoes);
                this.calculateButcherAge(this.does);
                this.calculateSuirvivalRate(this.does);
                this.calculateLiveAndDied(this.does);
                this.calculateMisses(data.topWeightDoes);

                this.$nextTick(function(){
                    $('#report-date-range').trigger('triggerDaterangepicker');
                    this.options.production.scales.yAxes[0].ticks.stepSize = this.stepSize();
                    this.$broadcast('show-chart');
                });
            });
        },
        'load-bucks'() {
            var req_data = {};
            this.loadingBucks = 1;
            if (this.report_date_range[0] && this.report_date_range[1]) {
                req_data = this.setDateFromDateRangePicker(this.report_date_range);
            }
            api.getReportsBucks(req_data).then(data => {
                this.loadingBucks = 0;
                this.calculateMisses(data.bucks, true);

                this.$nextTick(() => {
                    $('#report-date-range').trigger('triggerDaterangepicker');
                    this.$broadcast('show-chart');
                });
            });
        }
    },
    watch: {
        activeDoes: function(){
            this.calculateProduction(this.activeDoes);
        },
        report_date_range: function() {
            this.datasets = {
                production: [],
                meatProduction: [],
                butcherAge: [],
                suirvivalRate: [],
                liveAndDied:[],
                reasonsForDeath: [],
                missedBreedings: [],
                missedBucksBreedings: []
            };
            this.labels = {
                production: [],
                meatProduction: [],
                butcherAge: [],
                suirvivalRate: [],
                liveAndDied: [],
                reasonsForDeath: [],
                missedBreedings: [],
                missedBucksBreedings: []
            };
            this.$emit('load-statistics');
            this.$emit('load-does');
            this.$emit('load-bucks');
        }
    },
    methods: {
        showWeights: function(weight){
            if(this.weightSlug == ""){
                return this.calcLbsOunces(weight);
            }
            return parseFloat(weight) + " " + this.weightSlug;
        },

        stepSize: function(){
            if(this.weightSlug == ""){
                return 16;
            }
            return undefined;
        },

        calcLbsOunces: function(weight){
            if(weight == 0){
                return '0 lbs';
            }
            var lbs = parseFloat(weight/16);
            var tmp = lbs.toString().split('.');
            var txt = tmp[0] > 0 ? tmp[0] + " lbs " : '';
            var tmp2 = (lbs - tmp[0]) * 16;
            txt += tmp2 > 0 ? parseFloat(tmp2.toFixed(3)) + ' oz' : '';
            return txt;
        },

        setActiveDoe: function(doe){
            this.activeDoes = [ doe ];
        },
        setActiveDoes: function(){
            this.activeDoes = this.does;
        },
        toggleDoe: function(doe, index){
            console.log('toggle', index, this.activeDoes[index]);
            if(this.activeDoes[index] == {}){
                this.activeDoes[index] = doe;
            } else {
                this.activeDoes[index] = '';
            }
        },

        calculateProduction: function(doe){
            console.log('calculateProduction');
            var datasets = [];
            var labels = [];

            _.each(this.activeDoes, function(doe){
                var dataset = {};
                var allLittersWeighs = [];

                _.each(doe.litters, function(litter, key){
                    if(key < this.production.littersPerDoe) {

                        var weightSum = [];
                        var weights = [];
                        _.each(litter.survived_kits, function (kit) {
                            if(kit.weight){
                                weights.push(kit.weight)
                            }

                        });
                        for(var i = 0; i < weights.length; i++) {
                            for(var j = 0; j < weights[i].length; j++) {
                                if(typeof weightSum[j] === 'undefined'){
                                    weightSum[j] = 0.0;
                                }
                                weightSum[j] += parseFloat(weights[i][j]);
                                weightSum[j] = parseFloat(weightSum[j].toFixed(2));
                            }
                        }
                        weightSum.unshift(0);
                        if(key != this.production.littersPerDoe - 1){
                            weightSum.push(null);
                        }

                        if(weightSum.length) {
                            allLittersWeighs.push(weightSum);
                        }
                    }
                }.bind(this));

                dataset.data = allLittersWeighs;
                dataset.label = 'weight';
                dataset.fill = false;
                var doeExternalKey = _.findIndex(this.does, function(externalDoe){ return externalDoe.id == doe.id });
                _.extend(dataset, Chart.defaults.global.colours[doeExternalKey % Chart.defaults.global.colours.length]);//set colors
                dataset.lineTension = 0.1;
                datasets.push(dataset);
            }.bind(this));

            var weightsLengths = Array.apply(null, Array(this.production.littersPerDoe)).map(Number.prototype.valueOf,0);    //litters weights lines should start same point
            _.each(datasets, function(dataset, index){
                for(var i = 0; i< dataset.data.length; i++){
                    if(weightsLengths[i] < dataset.data[i].length) {
                        weightsLengths[i] = dataset.data[i].length;
                    }
                }
            });
            _.each(datasets, function(dataset, key){
                var data = [];
                for(var i = 0; i< dataset.data.length; i++){
                    var missedNulls = weightsLengths[i] - dataset.data[i].length;
                    if(missedNulls){
                        for (var j = 0; j < missedNulls; j++) {
                            dataset.data[i].push(null);
                        }
                    }
                    data = data.concat(dataset.data[i]);
                }
                dataset.data = data;
            });

            for(var i = 0; i < weightsLengths.length; i++){  //center labels
                for(var j = 0; j < weightsLengths[i]; j++){
                    if(Math.round(weightsLengths[i]/2 -1) == j){
                        labels.push('Litter ' + (i+1));
                    } else {
                        labels.push('');
                    }
                }

            }

            this.labels.production = labels;
            this.datasets.production = datasets;
        },

        calculateMeatProduction: function(does){
            console.log('calculateMeatProduction');
            var other = 0;
            var datasets = [];
            var meats = [];
            var labels = [];
            this.statistics.totalMeat = 0;
            _.each(does, function(doe, key){
                var dataset = {};
                var meat = parseFloat(doe.totalMeat);
                dataset.lable = doe.name;
                this.statistics.totalMeat += meat;
                if(key > this.meatProduction.limit - 1){
                    other += meat;
                } else {
                    labels.push(doe.name);
                    meats.push(meat);
                }
            }.bind(this));

            this.statistics.totalMeat = this.statistics.totalMeat.toFixed(2);

            if(other){
                meats.push(other);
                labels.push('other');
            }
            datasets.push({
                data: meats
            });
            _.each(datasets, function (dataset, index) {
                dataset.backgroundColor = [];
                _.each(Chart.defaults.global.colours, function (color, key) {
                    if(key < this.meatProduction.limit){
                        dataset.backgroundColor.push(color.borderColor);
                    } else {
                        dataset.backgroundColor.push(this.meatProduction.otherColor);

                    }
                }.bind(this))

            }.bind(this));

            this.datasets.meatProduction = datasets;
            this.labels.meatProduction = labels;
        },

        calculateMisses(breeders, bucks = false) {
            const misses = [],
                labels = [];
            let data = [];

            for (const breeder of breeders) {
                if (!breeder.misses_count) {
                    continue;
                }
                data.push({name: breeder.name, misses: breeder.misses_count});
            }

            data = _.sortBy(data, 'misses').reverse();
            for (const breeder of data) {
                misses.push(breeder.misses);
                labels.push(breeder.name);
            }

            if (bucks) {
                this.labels.missedBucksBreedings = labels;
                this.datasets.missedBucksBreedings.push({
                    data: misses,
                    label: '',
                    backgroundColor : "rgba(0,192,239,0.5)",
                    borderWidth: 1,
                    borderColor: "rgba(0,192,239,1)"
                });
            } else {
                this.labels.missedBreedings = labels;
                this.datasets.missedBreedings.push({ data: misses, label: ''});
            }
        },

        calculateButcherAge: function(does) {
            console.log('calculateButcherAge');
            var labels = [];
            var result = [];
            var days = [];
            _.each(does, function(doe, key) {
                var dataset = {};
                var totalDays = 0;
                var butcheredLitters = 0;
                var hasButchered = false;
                _.each(doe.litters, function(litter){
                    if(litter.butchered == 1){
                        var born = App.parseMoment(litter.born);
                        var butchered = moment(litter.butchered_at, 'YYYY-MM-DD H:i:s').startOf('day');
                        var days = butchered.diff(born, "days");
                        totalDays = totalDays + days;
                        butcheredLitters++;
                        hasButchered = true;
                    }
                });

                dataset.data = [Math.round(totalDays/butcheredLitters)];

                if(hasButchered) {
                    result.push({
                        name: doe.name,
                        days: Math.round(totalDays/butcheredLitters)
                    });
                }
            });

            result = _.sortBy(result, 'days');
            _.each(result.reverse(), function(item) {
                labels.push(item.name);
                days.push(item.days);
            });

            this.labels.butcherAge = labels;
            this.datasets.butcherAge.push({ data: days, label: ''});

        },

        calculateSuirvivalRate: function(does){
            console.log('calculateSuirvivalRate');
            var labels = [];
            var result = [];
            var survivalRateTotal = [];
            _.each(does, function(doe, key){
                var survivalRate = 0;
                _.each(doe.litters, function(litter){
                    survivalRate = survivalRate + parseFloat(litter.survival_rate);
                });
                if(doe.litters_count) {
                    result.push({
                        name: doe.name,
                        survivalRate: parseFloat((survivalRate / doe.litters_count).toFixed(1))
                    });
                }
            });

            result = _.sortBy(result, 'survivalRate');
            _.each(result.reverse(), function(item){
                labels.push(item.name);
                survivalRateTotal.push(item.survivalRate);
            });
            this.labels.suirvivalRate = labels;
            this.datasets.suirvivalRate.push({
                data: survivalRateTotal,
                label: '',
                backgroundColor : "rgba(0,166,90,0.5)",
                borderWidth: 1,
                borderColor: "rgba(0,166,90,1)"
            });

        },

        calculateLiveAndDied: function(does){
            console.log('calculateLiveAndDied');
            var labels = [];
            var result = [];

            _.each(does, function(doe, key){
                var liveKits = 0;
                var diedKits = 0;
                _.each(doe.litters, function(litter){
                    var died = litter.kits_died == null? 0: litter.kits_died;
                    liveKits += parseInt(litter.kits_amount - died);
                    diedKits += parseInt(died);
                });
                if(liveKits || diedKits) {
                    result.push({
                        name: doe.name,
                        liveKits: liveKits,
                        diedKits: diedKits
                    });
                }
            });

            var liveKits = [];
            var diedKits = [];

            result = _.sortBy(result, 'liveKits');
            _.each(result.reverse(), function(item){
                labels.push(item.name);
                liveKits.push(item.liveKits);
                diedKits.push(item.diedKits);
            });

            this.labels.liveAndDied = labels;
            this.datasets.liveAndDied.push({
                data: liveKits,
                backgroundColor : "rgba(0,166,90,0.5)",
                borderWidth: 1,
                borderColor: "rgba(0,166,90,1)"
            });
            this.datasets.liveAndDied.push({
                data: diedKits,
                backgroundColor : "rgba(255,80,90,0.5)",
                borderWidth: 1,
                borderColor: "rgba(255,80,90,1)"
            });

        },

        showReasonsForDeath: function() {
            if(this.statistics.reasonsForDeath){
                let reasons = this.statistics.reasonsForDeath;
                let labels = [];
                let data = [];
                _.each(reasons, function(count, reason){
                    labels.push(reason);
                    data.push(count);
                });

                if(labels.length > 0) {
                    this.showReasonForDeathChart = true;
                }

                this.labels.reasonsForDeath = labels;
                this.datasets.reasonsForDeath.push({
                    data: data,
                    backgroundColor : "rgba(255,80,90,0.5)",
                    borderWidth: 1,
                    borderColor: "rgba(255,80,90,1)"
                });
                console.log('calculateReasonsForDeath');
            }
        },

        setDateFromDateRangePicker: function(daterange) {
            var retrun_val = {
                from: null,
                to: null
            }
            retrun_val.from = daterange[0].format('YYYY-MM-DD');
            retrun_val.to = daterange[1].format('YYYY-MM-DD');
            return retrun_val;
        }
    },
    ready: function () {
        this.$emit('load-statistics');
        this.$emit('load-does');
        this.$emit('load-bucks');
    },
    mixins: [App.Mixins.Currency]
};
