<div class="modal modal-danger" id="delete-pure-modal">
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
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
