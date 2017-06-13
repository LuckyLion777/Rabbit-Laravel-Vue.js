<!-- modal -->
<div class="modal" id="pedigree-form">
    <div class="modal-dialog">
        <div class="modal-content">
            <pedigree-form :breeder.sync="breeder" :breeders.sync="breeders"></pedigree-form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<copy-pedigree-form v-if="{{ $id }}" :id="{{ $id }}" type="{{ $type }}" :name="{{ $name }}"></copy-pedigree-form>
