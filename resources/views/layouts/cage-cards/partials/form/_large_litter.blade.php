<div class="row" v-if="template.type == 'litter' && template.size == '4_7_large' && template.size == '4_7_large' && !previewItem">
    <div class="box box-default">
        <div class="box-body box-profile">
            <div class="row ">
                <div class="col-xs-4"><img
                            src="/img/pedigree_logo.png"
                            style="max-width:100%"><br><br>
                </div>
                <div class="col-xs-8">

                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 1])
                    </div>
                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 2])
                    </div>

                </div>

            </div>
            <div class="row ">
                <div class="col-xs-3"><img
                            src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                            style="max-width:100%">
                </div>
                <div class="col-xs-3">
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 3])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 4])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 5])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 6])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 7])
                </div>
                <div class="col-xs-3">
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 8])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 9])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 10])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 11])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 12])
                </div>
                <div class="col-xs-3">
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 13])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 14])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 15])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 16])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 17])
                </div>


            </div>

            <div class="row "><br>
                <div class="form-group col-xs-8">
                    <label class="col-sm-4 control-label">Litter
                        Table</label>
                    <div class="col-sm-8">
                        <div class="col-xs-6">
                            <input v-on:change="addSelectedFieldToFields" name="table" v-bind:checked="template.fields['table'] !== undefined && template.fields['table'] == 'yes'" type="radio" value="table:yes">
                            Yes
                        </div>
                        <div class="col-xs-6">
                            <input v-on:change="addSelectedFieldToFields" name="table" v-bind:checked="template.fields['table'] === undefined || template.fields['table'] == 'no'" checked type="radio" value="table:no">
                            No
                        </div>


                    </div>
                </div>
            </div>


        </div><!-- /.box-body -->
    </div>
    <button @click="getPreview()" class="btn btn-info  col-xs-offset-1"
            type="button"><i
                class="fa fa-search"></i> Preview
    </button>
    <br><br><br><br>
</div>
