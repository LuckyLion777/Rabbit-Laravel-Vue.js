<div class="row" v-if="template.type == 'breeder' && template.size == '2_3_business' && previewItem == 'breeder.2_3_business' && template.orientation == 'vertical'">
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


                    <div class="col-xs-6 col-xs-offset-3"><img
                                src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                                style="max-width:100%"><br></div>
                </div>


                <h4 class="text-center">
                    <small>@{{{ getPreviewFieldValue(firstBreeder, 1, 'El Sueno\'s') }}}</small>
                    <strong>@{{{ getPreviewFieldValue(firstBreeder, 2, 'Sweet Mamasi') }}}</strong><br>
                    @{{{ getPreviewFieldValue(firstBreeder, 3, '1225337') }}}
                </h4>
                <div class="row box-profile__properties">
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 4)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 4, 'Cage') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 4, '3') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 5)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 5, 'Sex') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 5, 'doe') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 6)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 6, 'Weight') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 6, '12 lbs') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 7)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 7, 'Breed') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 7, 'New Zealand') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 8)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 8, 'Color') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 8, 'Black with spots') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 9)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 9, 'Acquired') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 9, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 10)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 10, 'Born') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 10, '10/08/2013')  }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 11)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 11, 'Father') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 11, 'Midnight') }}}
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <p v-if="getPreviewFieldValue(firstBreeder, 12)">
                            <strong> @{{ getPreviewFieldName(firstBreeder, 12, 'Mother') }}</strong>
                            @{{{ getPreviewFieldValue(firstBreeder, 12, 'Dozy') }}}
                        </p>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        <button class="btn btn-default" @click="previewItem = ''" type="button">
            <i class="fa fa-pencil"></i> Edit Fields
        </button>
    </div>
</div>

