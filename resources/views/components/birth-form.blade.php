<template id="birth-form-template">
    <div>
        <div class="modal" id="birth-form-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Report Birth</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal row-paddings-compensation">
                        <div class="row">
                            <div class="form-group col-sm-6 col-xs-7">
                                <label class="col-sm-4 control-label">Breeders</label>
                                <div class="col-sm-8">
                                    <select class="form-control" v-model="activeBirth.breedplan">
                                        <option value="-1">Select plan</option>
                                        <option v-for="plan in plans" value="@{{ plan.id }}">@{{ shortDate(plan.date) }}  @{{ plan.name }}</option>
                                    </select>
                                </div>
                            </div>
                            
                         </div>
                         <div class="row">

                            <div class="form-group col-sm-6 col-xs-7">
                                <label class="col-sm-4 control-label">Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" v-datepicker="activeBirth.born" container="#birth-form-modal">
                                        <input type="text" class="form-control">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-th"></i>
                                                </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.given_id }">
                                <label class="col-sm-4 control-label">Litter ID</label>
                                <div class="col-sm-8">
                                    <input type="text" placeholder="Litter Number" v-model="activeBirth.given_id" class="form-control">
                                    <small class="error" v-if="errors.given_id">@{{ errors.given_id[0] }}</small>
                                </div>
                            </div>

                            
                        </div>
                         <div class="row">
                             <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.kits_amount }">
                                <label class="col-sm-4 control-label">#Live&nbsp;Kits</label>
                                <div class="col-sm-8">
                                    <input type="text" placeholder="Number Born Live" v-model="activeBirth.kits_amount"
                                           class="form-control">
                                    <small class="error" v-if="errors.kits_amount">@{{ errors.kits_amount[0] }}</small>
                                </div>

                            </div>
                             
                             <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.kits_amount }">
                                 <label class="col-sm-4 control-label">#Dead&nbsp;Kits</label>
                                 <div class="col-sm-8">
                                     <input type="text" placeholder="Number Born Dead" v-model="activeBirth.dead_kits_amount"
                                            class="form-control">
                                     <small class="error" v-if="errors.dead_kits_amount">@{{ errors.dead_kits_amount[0] }}</small>
                                 </div>

                             </div>
                         </div>
                         <div class="row">
                            <div class="form-group col-sm-12">
                                <label class="col-sm-2 control-label">Notes</label>
                                <div class="col-sm-10"><textarea v-model="activeBirth.notes" placeholder="Descriptions" rows="3"
                                                                 class="form-control"></textarea></div>
                            </div>
                          </div>

                        </form>
                    </div>
                    <div class="modal-footer bg-success">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close
                        </button>
                        <button v-if="activeBirth.breedplan != -1" type="button" @click="showMissed(activeBirth.breedplan)" class="btn btn-danger"><i class="fa fa-ban"></i> Missed</button>
                        <button type="button" @click="recordBirth" class="btn btn-success">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div class="modal modal-success" id="birth-schedule-next-modal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3>Do you want to schedule the next breed for this doe?</h3>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="col-sm-12 text-center">
                                <button v-if="doe && doe.id" class="btn btn-outline" type="button" @click="scheduleNextBreed(doe.id)"><i class="fa fa-check"></i> Yes</button>
                                <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No</button>
                            </div>
                        </div>

                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        @include('layouts.missing', ['unique' => '-birth-plan'])

    </div>
</template>