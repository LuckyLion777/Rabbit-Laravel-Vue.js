<div class="row"
     v-if="template.type == 'litter' && template.size == '4_7_large' && previewItem == 'litter.4_7_large'">
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
                <div class="col-xs-3"><img
                            src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                            style="max-width:100%"></div>
                <div class="col-xs3">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstLitter, 3, 'ID') }} </strong>@{{{ getPreviewFieldValue(firstLitter, 3, '1225337') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 4, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 4, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 5, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 5, '10/08/2013') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 6, 'Live') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 6, '10') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 7, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 7, '2') }}}
                    </p>
                </div>
                <div class="col-xs-3">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstLitter, 8, 'Live') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 8, '10') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 9, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 9, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 10, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 10, '80%') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 11, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 11, '80%') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 12, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 12, '80%') }}}
                    </p>
                </div>
                <div class="col-xs-3">
                    <p class="">
                        <strong>@{{ getPreviewFieldName(firstLitter, 13, 'Live') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 13, '10') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 14, 'Died') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 14, '2') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 15, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 15, '80%') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 16, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 16, '80%') }}}
                        <br><strong>@{{ getPreviewFieldName(firstLitter, 17, 'Survival') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 17, '80%') }}}
                    </p>
                </div>


            </div>

            <div class="row"
                 v-if="template.fields['table'] !== undefined && template.fields['table'] == 'yes'">
                <div class="col-xs-12">
                    <table class="table table-striped table-condensed">
                        <tbody v-if="firstLitter.rabbit_kits">
                            <tr v-for="kit in firstLitter.rabbit_kits">
                                <td>@{{ kit.id }}</td>
                                <td>@{{ kit.color }}</td>
                                <td>
                                    <i v-if="kit.sex == 'buck'" class="fa fa-mars"></i>
                                    <i v-if="kit.sex == 'doe'" class="fa fa-venus"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-if="!firstLitter.rabbit_kits">
                            <tr>
                                <td>11223344</td>
                                <td>White</td>
                                <td><i class="fa fa-mars"></i></td>
                            </tr>
                            <tr>
                                <td>11224455</td>
                                <td>Black</td>
                                <td><i class="fa fa-venus"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div>
    <button class="btn btn-default col-xs-offset-1" @click="previewItem = ''" type="button">
        <i class="fa fa-pencil"></i> Edit Fields
    </button>
</div>