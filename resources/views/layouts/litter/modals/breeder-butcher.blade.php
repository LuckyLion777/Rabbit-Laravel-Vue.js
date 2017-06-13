<!--- Butcher --->
<div id="breeder-butcher-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <breeder-butcher :breeder="breeder"></breeder-butcher>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div>

<!-- Butcher value -->
<div id="breeder-butcher-value" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Butcher Value</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal row-paddings-compensation">
                    <p class="lead">What is the total value of this butcher?</p>
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
