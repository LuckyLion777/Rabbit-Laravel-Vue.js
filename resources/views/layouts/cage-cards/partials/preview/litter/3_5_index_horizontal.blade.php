<div class="row"
     v-if="template.type == 'litter' && template.size == '3_5_index' && previewItem == 'litter.3_5_index' && template.orientation == 'horizontal'">
    <div class="box box-default">
        <div class="box-body box-profile col-min-height">
            <div class="row ">
                <div class="col-xs-4"><img
                            src="/img/pedigree_logo.png"
                            style="max-width:100%"><br><br></div>
                <div class="col-xs-8">
                    <h3>
                        <strong>@{{{ getPreviewFieldValue(firstLitter, 1, 'Blanca +') }}}</strong>
                        <strong>@{{{ getPreviewFieldValue(firstLitter, 2, 'Midnight') }}}</strong>
                    </h3>
                </div>

            </div>
            <div class="row ">
                <div class="col-xs-4"><img
                            src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                            style="max-width:100%"></div>
                <div class="col-xs-4">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstLitter, 3, 'ID') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 3, '1225337') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 4, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 4, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 5, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 5, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 5, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 6, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 7, 'Live') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 7, '10') }}}
                    </p>
                </div>
                <div class="col-xs-4">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstLitter, 8, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 8, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 9, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 9, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 10, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 10, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 11, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 11, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 12, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 12, '2') }}}
                    </p>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div>
    <button class="btn btn-default col-xs-offset-1" @click="previewItem = ''" type="button">
        <i class="fa fa-pencil"></i> Edit Fields
    </button>

</div>



