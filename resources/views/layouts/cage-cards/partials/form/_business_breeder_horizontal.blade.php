<div class="row" v-if="template.type == 'breeder' && template.size == '2_3_business' && template.orientation == 'horizontal' && !previewItem">
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
                <div class="col-xs-4"><img
                            src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                            style="max-width:100%">
                </div>
                <div class="col-xs-4">
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 3])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 4])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 5])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 6])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 7])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 8])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 9])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 10])
                    @include('layouts.cage-cards.partials.form._select_fields', ['id' => 11])

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
