<div class="row" v-if="template.type == 'litter' && template.size == '2_3_business' && template.orientation == 'vertical' && !previewItem">
    <div class="col-sm-6 col-sm-offset-3">
        <div class="box box-default">
            <div class="box-body box-profile">
                <div class="row ">
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '1'">
                        <img src="/img/business_hole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '0'">
                        <img src="/img/business_nohole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                </div>

                <div class="row ">


                    <div class="col-xs-6 col-xs-offset-3">
                        <img src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                             style="max-width:100%"><br><br>
                    </div>
                </div>


                <div class="row box-profile__properties">
                    <div class="col-xs-8 col-xs-offset-2">

                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 1])
                    </div>
                    <div class="col-xs-8 col-xs-offset-2">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 2])
                    </div>
                    <div class="col-xs-8 col-xs-offset-2">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 3])


                    </div>
                </div>
                <div class="row">


                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 4])
                    </div>

                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 5])
                    </div>
                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 6])
                    </div>
                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 7])
                    </div>
                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 8])
                    </div>
                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 9])
                    </div>
                    <div class="col-xs-8">
                        @include('layouts.cage-cards.partials.form._select_fields', ['id' => 10])
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
