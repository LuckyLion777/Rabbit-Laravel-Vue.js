<div class="modal modal-danger" id="delete-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-fw fa-warning"></i><br>
                            Do you want to delete @{{ confirmTarget }}?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="delete"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                        <button data-dismiss="modal" class="btn btn-outline" @click="archive" type="button"><i
                                    class="fa fa-archive"></i> Archive
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal modal-success" id="sold-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3>Do you want to mark @{{ confirmTarget }} sold?</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 text-right">
                        <label class="control-label" for="sold-value{{ @$unique ?: '' }}">Value</label>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <input type="text" id="sold-value{{ @$unique ?: '' }}" v-model="soldValue" class="form-control">
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="sold"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal modal-info" id="transfer-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-center">Do you want to transfer @{{ confirmTarget }}?</h3>
                        <p class="lead">
                            To transfer this rabbit to another Hutch user, please specify their email address. None of your personal data or other records will be transferred.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 text-right">
                        <label class="control-label" for="transfer-to{{ @$unique ?: '' }}">Recipient email</label>
                    </div>
                    <div class="col-xs-6" :class="{ 'has-error': transferErrors.email }">
                        <input type="email" id="transfer-to{{ @$unique ?: '' }}" v-model="transferTo" class="form-control">
                        <small class="error" v-if="transferErrors.email">@{{ transferErrors.email[0] }}</small>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="transfer"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-warning" id="confirm-transfer-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-center">Are you sure you want to transfer @{{ confirmTarget }}?</h3>
                        <p class="lead text-center">
                            We will transfer @{{ confirmTarget }} to the email address: <strong>@{{ transferTo }}</strong>
                            <br />This operation cannot be undone.
                        </p>
                    </div>
                </div>
               <div class="row">
                    <div class="col-xs-6 text-right">
                        <label class="control-label" for="transfer-to{{ @$unique ?: '' }}">Recipient email</label>
                    </div>
                    <div class="col-xs-6">
                        <span class="form-control-static">@{{ transferTo }}</span>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="confirmTransfer"><i class="fa fa-check"></i> Confirm</button>
                        <button type="button" class="btn btn-outline" @click="unconfirmTransfer"><i class="fa fa-close"></i> Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-danger" id="decline-transfer-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-center">Do you want to decline @{{ confirmTarget }}?</h3>
                        <p class="lead">
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="declineTransfer"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-default" id="unsold-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-fw fa-info"></i><br>
                            Do you want to unmark @{{ confirmTarget }} sold?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-default" type="button" @click="unsold"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal modal-default" id="archive-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body bg-gray">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-archive"></i><br>
                            Do you want to archive @{{ confirmTarget }}?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-default" type="button" @click="archive"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                        <button data-dismiss="modal" class="btn btn-default" @click="delete" type="button"><i
                                    class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-default" id="unarchive-breed-modal{{ @$unique ?: '' }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body bg-gray">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-expand"></i><br>
                            Do you want to unarchive @{{ confirmTarget }}?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-default" type="button" @click="unarchive"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                    class="fa fa-close"></i> No
                        </button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
