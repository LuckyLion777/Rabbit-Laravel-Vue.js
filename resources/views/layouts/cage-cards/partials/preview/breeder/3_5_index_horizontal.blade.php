<div class="row" v-if="template.type == 'breeder' && template.size == '3_5_index' && previewItem == 'breeder.3_5_index' && template.orientation == 'horizontal'">
    <div class="box box-default">
        <div class="box-body box-profile col-min-height">
            <div class="row ">
                <div class="col-xs-4"><img
                            src="/img/pedigree_logo.png"
                            style="max-width:100%"><br><br></div>
                <div class="col-xs-8">
                    <h3>
                        <small>@{{{ getPreviewFieldValue(firstBreeder, 1, 'El Sueno\'s') }}}</small>
                        <strong>@{{{ getPreviewFieldValue(firstBreeder, 2, 'Sweet Mamasi') }}}</strong></h3>
                </div>

            </div>
            <div class="row ">
                <div class="col-xs-4"><img
                            src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                            style="max-width:100%"></div>
                <div class="col-xs-4">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstBreeder, 3, 'ID')  }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 3, '1225337') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 4, 'Sex') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 4, 'doe') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 5, 'Cage') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 5, '3/4') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 6, 'Weight') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 6, '10 lbs') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 7, 'Color') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 7, 'White') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 8, 'Acquired') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 8, '10/08/2013') }}}
                    </p>
                </div>


                <div class="col-xs-4">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstBreeder, 9, 'Born') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 9, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 10, 'Cage') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 10, '3/4') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 11, 'Breed') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 11, 'NZ') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 12, 'Color') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 12, 'White') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 13, 'ID') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 13, '1225337') }}}
                        <br><strong>@{{ getPreviewFieldName(firstBreeder, 14, 'Sex') }} </strong>@{{{ getPreviewFieldValue(firstBreeder, 14, 'doe') }}}
                    </p>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div>
    <button class="btn btn-default col-xs-offset-1" @click="previewItem = ''" type="button">
        <i class="fa fa-pencil"></i> Edit Fields
    </button>
</div>

