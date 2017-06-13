<div class="row" v-if="template.type == 'litter' && template.size == '3_5_index'&& template.orientation == 'vertical' && !previewItem">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="box box-default">
            <div class="box-body box-profile">
                <div class="row ">
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '1'">
                        <img src="/img/index_hole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '0'">
                        <img src="/img/index_nohole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                </div>

                <div class="row ">

                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 1])
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 2])
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 3])


                    </div>
                    <div class="col-xs-6"><img
                                src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                                style="max-width:100%"><br><br>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 4])
                    </div>


                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 5])
                    </div>

                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 6])
                    </div>


                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 7])
                    </div>

                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 8])
                    </div>


                    <div class="col-xs-6">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 9])
                    </div>

                </div>
                <div class="row"><br>
                    <div class="form-group col-xs-12">
                        <label class="col-sm-4 control-label">Litter
                            Table</label>
                        <div class="col-sm-8">
                            <div class="col-xs-6">
                                <input v-on:change="addSelectedFieldToFields" name="table" v-bind:checked="template.fields['table'] !== undefined && template.fields['table'] == 'yes'" type="radio" value="table:yes">
                                Yes
                            </div>
                            <div class="col-xs-6">
                                <input v-on:change="addSelectedFieldToFields" name="table" v-bind:checked="template.fields['table'] === undefined || template.fields['table'] == 'no'" type="radio" value="table:no">
                                No
                            </div>


                        </div>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        <button @click="getPreview()" class="btn btn-info pull-right"
                type="button"><i
                    class="fa fa-search"></i>
            Preview
        </button>
        <br><br><br><br></div>
</div>