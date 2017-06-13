<div class="row"
     v-if="template.type == 'litter' && template.size == '3_5_index' && previewItem == 'litter.3_5_index' && template.orientation == 'vertical'">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="box box-default box-35">
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
                    <div class="col-xs-6">
                        <h3>
                            <strong>@{{{ getPreviewFieldValue(firstLitter, 1, 'Blanca') }}}</strong>
                            <br>
                            <strong>@{{{ getPreviewFieldValue(firstLitter, 2, 'Midnight') }}}</strong>
                            <br>
                            @{{{ getPreviewFieldValue(firstLitter, 3, '1225337') }}}
                        </h3>
                    </div>
                    <div class="col-xs-6"><img
                                src="https://blog-trycontechnologi.netdna-ssl.com/blog/wp-content/uploads/2016/07/qr-code-history-qr-code.png?x39658"
                                style="max-width:100%"><br><br></div>
                </div>
                <div class="row box-profile__properties">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 4)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 4, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 4, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 5)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 5, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 5, '10/08/2013') }}}
                        </p>
                    </div>
                </div>
                <div class="row box-profile__properties">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 6)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 6, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 6, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 7)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 7, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 7, '10/08/2013') }}}
                        </p>
                    </div>
                </div>
                <div class="row box-profile__properties">
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 8)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 8, 'Bred') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 8, '10/08/2013') }}}
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="" v-if="getPreviewFieldValue(firstLitter, 9)">
                            <strong>@{{ getPreviewFieldName(firstLitter, 9, 'Born') }} </strong> @{{{ getPreviewFieldValue(firstLitter, 9, '10/08/2013') }}}
                        </p>
                    </div>
                </div>

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

        <button class="btn btn-default" @click="previewItem = ''" type="button">
        <i class="fa fa-pencil"></i> Edit Fields
        </button>
    </div><!-- /.box-body -->

</div>



