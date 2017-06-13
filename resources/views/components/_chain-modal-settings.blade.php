<!-- modal -->
<div class="modal" id="new_chain">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span
                            aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">New Chain Item</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal row-paddings-compensation">


                    <div class="row">
                        <div class="form-group col-xs-7 col-sm-6">
                            <label class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8"><input class="form-control" placeholder="Enter ..." type="text"
                                                         v-model="chainName"></div>
                        </div>

                        <div class="form-group col-xs-7 col-sm-6">
                            <label class="col-sm-4 control-label">Days</label>
                            <div class="col-sm-8"><input placeholder="Enter ..." class="form-control" type="number"
                                                         v-model="chainDays"></div>
                        </div>
                    </div>

                    <div class="row">


                        <div class="form-group col-sm-12">
                            <label class="col-sm-2 control-label">Icon</label>
                            <div class="col-sm-8">
                                <div class="select-icon-of-task">
                                    <label><input v-model="chainIcon" value="fa-cutlery bg-red" name="selecticon"
                                                  type="radio" checked><i class="fa fa-cutlery icon-circle"
                                                                          v-bind:class="{'bg-red': iconBackground['bg-red']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-venus-mars bg-blue" name="selecticon"
                                                  type="radio"><i class="fa fa-venus-mars icon-circle"
                                                                  v-bind:class="{'bg-blue': iconBackground['bg-blue']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-check bg-maroon" name="selecticon"
                                                  type="radio"><i class="fa fa-check icon-circle"
                                                                  v-bind:class="{'bg-maroon': iconBackground['bg-maroon']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-birthday-cake bg-green"
                                                  name="selecticon" type="radio"><i
                                                class="fa fa-birthday-cake icon-circle"
                                                v-bind:class="{'bg-green': iconBackground['bg-green']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-balance-scale bg-yellow"
                                                  name="selecticon" type="radio"><i
                                                class="fa fa-balance-scale icon-circle"
                                                v-bind:class="{'bg-yellow': iconBackground['bg-yellow']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-calendar bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-calendar icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-calendar']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-heart bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-heart icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-heart']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-asterisk bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-asterisk icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-asterisk']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-bookmark bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-bookmark icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-bookmark']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-eye bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-eye icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-eye']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-flag bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-flag icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-flag']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-medkit bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-medkit icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-medkit']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-paw bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-paw icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-paw']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-trophy bg-gray" name="selecticon"
                                                  type="radio"><i class="fa fa-trophy icon-circle"
                                                                  v-bind:class="{'bg-black': iconBackground['fa-trophy']}"></i></label>
                                    <label><input v-model="chainIcon" value="fa-inbox bg-purple" name="selecticon"
                                                  type="radio"><i class="fa fa-inbox icon-circle"
                                                                  v-bind:class="{'bg-purple': iconBackground['bg-purple']}"></i></label>
                                </div>

                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" @click.prevent="addChain()">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


