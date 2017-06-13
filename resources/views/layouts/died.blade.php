<div class="modal modal-danger" id="died-modal{{ $unique }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-fw fa-warning"></i><br>
                            Are you sure you want to mark @{{ confirmTarget }} as dead?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="died"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No</button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-danger" id="death-reason-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">

                <form class="form-horizontal row-paddings-compensation">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3>Why did this rabbit die?</h3>
                            <label class="control-label col-md-6" for="reasonValue">Cause of Death</label>

                            {{--<div class="col-md-6">--}}
                            {{--<input type="text" v-el:reason v-model="reasonValue" class="form-control input-white" style="background-color: white">--}}
                            {{--</div>--}}

                            <div class="col-md-6">
                                <div class="input-group">
                                    <select v-model="reasonValue" class="form-control">
                                        <option :value="reason" v-for="reason in deathReasons">@{{reason}}</option>
                                    </select>
                                    <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" @click="addNewFunc">New</button>
                                </span>
                                </div>
                                <div v-if="addNew" class="col-md-12">
                                    <form>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="newDeathReason" id="newReason" placeholder="Death Reason">
                                        </div>

                                        <div class="btn-group btn-group-justified" role="group">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-success" @click.prevent="addNewReason">Save</button>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-default" @click.prevent="reasonCancel">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-danger">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                <button class="btn btn-danger" type="button" @click="sendReasonValue">Save</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
