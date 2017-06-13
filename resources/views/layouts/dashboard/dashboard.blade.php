<template id="dashboard-template">

    <section class="content">


        <div class="row quick-tasks">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua" @click="breedModal" id="step1">

                    <div class="inner">
                        <h3>Breed</h3>
                        <p>Add new breeding</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-venus-mars"></i>
                    </div>
                    <a href="#" class="small-box-footer">Start <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6" id="birthModal" @click="birthModal">
                <!-- small box -->
                <div class="small-box bg-green" id="step2">
                    <div class="inner">
                        <h3>Birth</h3>
                        <p>Record a birth</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-birthday-cake"></i>
                    </div>
                    <a href="#" class="small-box-footer">Record  <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div id="weightModal" class="col-lg-3 col-xs-6" @click="weightModal()">
                <!-- small box -->
                <div class="small-box bg-yellow" id="step3">
                    <div class="inner">
                        <h3>Weigh</h3>
                        <p>Litter performance</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-balance-scale"></i>
                    </div>
                    <a href="#" class="small-box-footer">Enter Data <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6" @click="butcherModal()">
                <!-- small box -->
                <div class="small-box bg-red" id="step4">
                    <div class="inner">
                        <h3>Butcher</h3>
                        <p>Dispatch litters</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-cutlery"></i>
                    </div>
                    <a href="#" class="small-box-footer">Record event <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        </div>

        <div class="row">
            <upcomming-tasks></upcomming-tasks>
            <schedule-calendar></schedule-calendar>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6"><a href="#" role="button" @click.prevent="newBreeder">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content text-muted"><h1>New Breeder</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div><div class="col-lg-4 col-md-6 col-sm-6"><a @click.prevent="newLitter" role="button" href="#">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content text-muted"><h1>New Litter</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6"><a v-link="{ path: '/ledger', query: {new: true} }"
                                                        role="button" href="#">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray text-muted"><i class="fa fa-plus"></i></span>
                        <div class="info-box-content text-muted"><h1>New Ledger</h1>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div>
        </div>


        <!-- modal -->

        @include("layouts.litter.modals.litter")
        @include("layouts.breeders.partials.breeder")
    </section>

    <birth-form :plans.sync="plans" :active-birth.sync="activeBirth"></birth-form>

    <div id="litter-weight-modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <litter-weight :litter="litter" :litters="litters" :kits="activeKits"></litter-weight>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!--- Butcher --->
    <div id="litter-butcher-modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <litter-butcher :litters="litters" :litter="litter" :kits="activeKits"></litter-butcher>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- Butcher value -->
    <div id="litter-butcher-value" class="modal modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form class="form-horizontal row-paddings-compensation">
                        <p class="lead text-center">What is the total value of this butcher?</p>
                        <div class="row">
                            <label class="control-label col-md-6" for="butcher-value">Value</label>
                            <div class="col-md-6">
                                <input id="butcher-value" type="number" v-model="butcherValue" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-danger">
                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                    <button class="btn btn-danger" type="button" @click="sendButcherValue">Save</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>


</template>
