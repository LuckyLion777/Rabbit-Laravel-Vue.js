<div class="row" v-if="template.type == 'litter' && template.size == '2_3_business' && previewItem == 'litter.2_3_business' && template.orientation == 'vertical'">
    <div class="col-sm-6 col-sm-offset-3">
        <div class="box box-default">
            <div class="box-body box-profile col-min-height">
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
                                style="max-width:100%"><br>
                    </div>
                </div>
                <h3 class="text-center">
                    <strong>@{{{ getPreviewFieldValue(firstLitter, 1, 'Midnight') }}}</strong><br>
                    <strong>@{{{ getPreviewFieldValue(firstLitter, 2, 'Blanca') }}}</strong><br>
                    @{{{ getPreviewFieldValue(firstLitter, 3, '1225337') }}}
                </h3>
                <div class="row box-profile__properties">
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 4)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 4, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 4, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 5)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 5, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 5, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 6)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 6, 'Live Kits') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 6, '4') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 7)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 7, 'Died') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 7, '2') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 8)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 8, 'Died') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 8, '2') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 9)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 9, 'Died') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 9, '2') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstLitter, 10)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 10, 'Died') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 10, '2') }}}
                        </p>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        <button class="btn btn-default" @click="previewItem = ''" type="button">
            <i class="fa fa-pencil"></i>
            Edit Fields
        </button>
    </div>
</div>
