<!--- Butcher --->
<div id="litter-butcher-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <litter-butcher :litters{{ empty($nosync) ? '.sync' : '' }}="{{ @$litters ?: 'litters' }}"
                            :litter{{ empty($nosync) ? '.sync' : '' }}="{{ @$litter ?: 'activeLitter' }}"
                            :kits{{ empty($nosyncKits) ? '.sync' : '' }}="{{ @$kits ?: 'activeKits' }}"
                            @if(isset($refresh)) @refresh-kits="{{ $refresh }}" @endif
            ></litter-butcher>
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
