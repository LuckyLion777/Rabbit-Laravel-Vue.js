<div class="row" v-if="template.type == 'breeder' && template.size == '3_5_index' && template.orientation == 'vertical' && previewItem == 'breeder.3_5_index'">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="box box-default">
            <div class="box-body box-profile col-min-height">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '1'">
                        <img src="/img/business_hole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                    <div class="col-xs-10 col-xs-offset-1" v-if="template.hole == '0'">
                        <img src="/img/business_nohole.png"
                             style="max-width:100%; width: 100%;"><br><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <h3><small>@{{{ getPreviewFieldValue(firstBreeder, 1, 'El Sueno\'s') }}}</small><br><strong>@{{{ getPreviewFieldValue(firstBreeder, 2, 'Sweet Mamasi') }}}</strong><br>@{{{ getPreviewFieldValue(firstBreeder, 3, '1225337') }}}</h3>
                    </div>
                    <div class="col-xs-6"><img src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658" style="max-width:100%"><br><br></div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 4)"><strong>@{{ getPreviewFieldName(firstBreeder, 4, 'ID') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 4, '1225337') }}}</p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 5)"><strong>@{{ getPreviewFieldName(firstBreeder, 5, 'Sex') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 5, 'doe') }}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 6)"><strong>@{{ getPreviewFieldName(firstBreeder, 6, 'Cage') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 6, '3/4') }}} </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 7)"><strong>@{{ getPreviewFieldName(firstBreeder, 7, 'Weight') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 7, '10 lbs') }}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 8)"><strong>@{{ getPreviewFieldName(firstBreeder, 8, 'Breed') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 8, 'NZ') }}}</p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 9)"><strong>@{{ getPreviewFieldName(firstBreeder, 9, 'Color') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 9, 'White') }}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 10)"><strong>@{{ getPreviewFieldName(firstBreeder, 10, 'Acquired') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 10, '10/08/2013') }}}</p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 11)"><strong>@{{ getPreviewFieldName(firstBreeder, 11, 'Born') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 11, '10/08/2013') }}}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 12)"><strong>@{{ getPreviewFieldName(firstBreeder, 12, 'Father') }} </strong>
                            <span>@{{{ getPreviewFieldValue(firstBreeder, 12, 'Midnight') }}}</span>
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 13)"><strong>@{{ getPreviewFieldName(firstBreeder, 13, 'Mother') }} </strong>
                            <span>@{{{ getPreviewFieldValue(firstBreeder, 13, 'Blanca') }}}</span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 14)"><strong>@{{ getPreviewFieldName(firstBreeder, 14, 'Father') }} </strong>
                            <span>@{{{ getPreviewFieldValue(firstBreeder, 14, 'Midnight') }}}</span>
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstBreeder, 15)"><strong>@{{ getPreviewFieldName(firstBreeder, 15, 'Mother') }} </strong>
                            <span>@{{{ getPreviewFieldValue(firstBreeder, 15, 'Blanca') }}}</span>
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

