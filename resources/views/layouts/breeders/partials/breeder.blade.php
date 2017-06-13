<!-- modal -->
<div class="modal" id="breeder-form">
    <div class="modal-dialog">
        <div class="modal-content">
            <breeder-form :breeder.sync="breeder" :breeders.sync="breeders"></breeder-form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal modal-danger" id="breeder-limit-alert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-center">You have exceeded the maximum amount of breeders</h3>
                        <p class="lead text-center">
                            Please upgrade your account to add more!
                        </p>
                    </div>
                </div>
                <div class="row margin">
                    <div class="col-sm-12 text-center">
                        <button type="button" class="btn btn-outline" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
