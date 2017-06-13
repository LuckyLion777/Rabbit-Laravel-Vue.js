<template id="reports-template">

    <section class="content-header">
        <h1>
            Reports
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" v-link="{ path: '/' }"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Reports</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">


        <!-- Your Page Content Here -->

        <div class="row">
            <div class="col-md-3 col-lg-2" v-if="!associated_type">
                <div class="form-group">
                  <label>Date range:</label>
                      <div class="input-group">
                          <input type="text" class="form-control pull-right" id="report-date-range" v-daterangepicker="report_date_range">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                      </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-venus-mars"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active Breeders</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.activeBreedersTotal }}</span><span
                                v-if="!loadingStatics" class="progress-description">@{{ statistics.archivedBreedersTotal }} Archived</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-th"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Litters</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.littersTotal }}</span><span
                                v-if="!loadingStatics" class="progress-description">@{{ statistics.butcheredLittersTotal }} Butchered</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon"><i class="fa fa-birthday-cake"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Kits Born</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.kitsTotal }}</span><span
                                v-if="!loadingStatics" class="progress-description">@{{ statistics.butcheredKitsTotal }} Butchered</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->


            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>

                    <div class="info-box-content">
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{{ currencySign() }}}@{{ (statistics.debits - statistics.credits).toFixed(2) }}</span>
                        <span v-if="!loadingStatics" class="progress-description">Income: @{{{ currencySign() }}}@{{ statistics.debits }}</span>
                        <span v-if="!loadingStatics" class="progress-description">Expenses: @{{{ currencySign() }}}@{{ statistics.credits }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- /.col -->
        </div>


        <div class="row">
            <!-- Left col -->
            <div class="col-md-8">
                <!-- MAP & BOX PANE -->
                <div class="box box-default">
                    <div class="box-header bg-gray">
                        <h3 class="box-title">Production</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="row" v-if="!(loadingDoes||loadingBucks)">
                            <div class="col-md-10 col-sm-8">
                                <div class="chart">
                                    <line-chart
                                            :height="320"
                                            :width="776"
                                            :type="'line'"
                                            :datasets="datasets.production"
                                            :labels="labels.production"
                                            :options="options.production">
                                    </line-chart>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-2 col-sm-4">
                                <div class="pad box-pane-right">
                                    <ul class="chart-legend clearfix">
                                        <li><a href="#" @click.prevent="setActiveDoes()"><i class="fa fa-circle text-green"></i> All</a></li>
                                        <li v-for="(index, doe) in does"><a href="#" @click.prevent="setActiveDoe(doe)"><i class="fa fa-circle-o text-@{{ colours[index % 10] }}"></i> @{{ doe.name }}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- /.row -->

                <!-- TABLE: LATEST ORDERS -->

                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <!-- Info Boxes Style 2 -->
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Kits</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.kitsTotal }} in @{{ statistics.littersTotal }} Litters</span><span v-if="!loadingStatics" class="progress-description">Ave Litter: @{{ Math.round(statistics.kitsTotal / statistics.littersTotal) }} kits</span>
                    </div><!-- /.info-box-content -->
                </div>
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-cutlery"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Age to Butcher</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.butchAverageWeeks }} wks / @{{ statistics.butchAverageDays }} days</span><span v-if="!loadingStatics" class="progress-description">Ave Butcher Weight: @{{ showWeights(statistics.butchAverageWeight) }}</span>
                    </div><!-- /.info-box-content -->
                </div>
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Survival Rate</span>
                        <h1 class="loader1" v-if="loadingStatics"><i class="fa fa-spin fa-spinner"></i></h1>
                        <span v-if="!loadingStatics" class="info-box-number">@{{ statistics.survivalRate }}%</span>
                        <span v-if="!loadingStatics" class="progress-description">@{{ statistics.kitsTotal - statistics.diedKitsTotal }} Live Kits, @{{ statistics.diedKitsTotal }} Died</span>

                    </div><!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->

                <!-- /.info-box -->

                <!-- /.info-box -->

                <!-- /.info-box -->


                <!-- /.box -->

                <!-- PRODUCT LIST -->

                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header bg-red">
                        <h3 class="box-title">Meat Produced</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="row" v-if="!(loadingDoes||loadingBucks)">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <line-chart
                                            :height="389"
                                            :width="776"
                                            type="doughnut"
                                            :datasets="datasets.meatProduction"
                                            :labels="labels.meatProduction"
                                            :options="options.meatProduction"
                                            legend="#meat-legend"
                                    >
                                    </line-chart>
                                </div>
                                <!-- ./chart-responsive -->
                            </div>
                            <div class="col-md-4">
                                <div id="meat-legend" class="chart-legend"></div>
                                <h4>Total: @{{ showWeights(statistics.totalMeat) }}</h4>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->

                    <!-- /.footer -->
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header bg-red">
                        <h3 class="box-title">Butcher Age</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="chart" v-if="!(loadingDoes||loadingBucks)">
                            <line-chart
                                    :height="249"
                                    :width="776"
                                    :type="'bar'"
                                    :datasets="datasets.butcherAge"
                                    :labels="labels.butcherAge"
                                    :options="options.butcherAge">
                            </line-chart>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- /.box-footer -->
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header bg-green">
                        <h3 class="box-title">Survival Rates</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="row" v-if="!(loadingDoes||loadingBucks)">
                            <div class="col-md-12">
                                <div class="chart-responsive">
                                    <line-chart
                                            :height="249"
                                            :width="776"
                                            :type="'bar'"
                                            :datasets="datasets.suirvivalRate"
                                            :labels="labels.suirvivalRate"
                                            :options="options.butcherAge">
                                    </line-chart>
                                </div>
                                <!-- ./chart-responsive -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->

                    <!-- /.footer -->
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header bg-green">
                        <h3 class="box-title">Live and Died Kits</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="chart" v-if="!(loadingDoes||loadingBucks)">
                            <line-chart
                                    :height="249"
                                    :width="776"
                                    :type="'bar'"
                                    :datasets="datasets.liveAndDied"
                                    :labels="labels.liveAndDied"
                                    :options="options.butcherAge">
                            </line-chart>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- /.box-footer -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header bg-maroon">
                        <h3 class="box-title">Doe Misses</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="chart" v-if="!(loadingDoes||loadingBucks)">
                            <line-chart
                                    :height="249"
                                    :width="776"
                                    :type="'bar'"
                                    :datasets="datasets.missedBreedings"
                                    :labels="labels.missedBreedings"
                                    :options="options.missedBreedings">
                            </line-chart>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- /.box-footer -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header bg-aqua">
                        <h3 class="box-title">Buck Misses</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h1 class="loader1" v-if="loadingDoes||loadingBucks"><i class="fa fa-spin fa-spinner"></i></h1>
                        <div class="chart" v-if="!(loadingDoes||loadingBucks)">
                            <line-chart
                                    :height="249"
                                    :width="776"
                                    :type="'bar'"
                                    :datasets="datasets.missedBucksBreedings"
                                    :labels="labels.missedBucksBreedings"
                                    :options="options.missedBucksBreedings">
                            </line-chart>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- /.box-footer -->
                </div>
            </div>
            <div class="col-md-6" v-if="showReasonForDeathChart">
                <div class="box box-danger">
                    <div class="box-header bg-red">
                        <h3 class="box-title">Cause of Death</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="chart">
                            <line-chart
                                    :height="249"
                                    :width="776"
                                    :type="'bar'"
                                    :datasets="datasets.reasonsForDeath"
                                    :labels="labels.reasonsForDeath"
                                    :options="options.reasonsForDeath">
                            </line-chart>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- /.box-footer -->
                </div>
            </div>
        </div>
    </section><!-- /.content -->


</template>
