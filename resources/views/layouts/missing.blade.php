<div class="modal modal-danger" id="missing-modal{{ $unique }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center"><h3><i class="fa fa-fw fa-warning"></i><br>
                        Are you sure you want to mark @{{ confirmTarget }} as missed?</h3>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-outline" type="button" @click="missed"><i class="fa fa-check"></i> Yes</button>
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> No</button>
                    </div>
                </div>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
