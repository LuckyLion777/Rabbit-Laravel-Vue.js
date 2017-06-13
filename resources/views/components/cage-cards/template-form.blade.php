<template id="cage-cards-template-form-template">
    <div class="modal-header bg-success"
         v-bind:class="{ 'bg-info': template.id }">
        <button type="button" @click="closeModalHandler()" class="close" data-dismiss="modal"
                aria-label="Close"><span
                    aria-hidden="true">×</span></button>
        <h4 class="modal-title">
            <span v-if="template.id">Edit</span>
            <span v-if="!template.id">New</span> Template
            <span class="loader1" v-if="loading">
                <i class="fa fa-spin fa-spinner"></i>
            </span>
        </h4>
    </div>
    <div class="modal-body">
        <form class="form-horizontal row-paddings-compensation">
            <input v-if="template.id != 0" name="_method" v-model="template._method" type="hidden" value="PUT">
            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Name</label>
                    <div class="col-sm-8" v-bind:class="{ 'has-error': errors.name }">
                        <input v-model="template.name" class="form-control" placeholder="Enter ..." type="text">
                        <small class="error" v-if="errors.name">@{{ errors.name[0] }}</small>
                    </div>
                </div>

                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Type</label>
                    <div class="col-sm-8" v-bind:class="{ 'has-error': errors.type }">
                        <select v-on:change="setFieldsOptions" v-model="template.type" class="form-control">
                            <option value="" disabled selected>Choose</option>
                            <option value="breeder">Breeder
                            </option>
                            <option value="litter">Litter
                            </option>
                        </select>
                        <small class="error" v-if="errors.type">@{{ errors.type[0] }}</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">

                    <label class="col-sm-4 control-label">Size</label>
                    <div class="col-sm-8" v-bind:class="{ 'has-error': errors.size }">
                        <select v-on:change="resetSelectableFieldsStatement"  v-model="template.size" class="form-control">
                            <option value="" disabled selected>Choose</option>
                            <option value="2_3_business">2 x 3 business
                                card
                            </option>
                            <option value="3_5_index">3 x 5 index
                                card
                            </option>
                            <option value="4_7_large">4 x 7 large
                                card
                            </option>
                        </select>
                        <small class="error" v-if="errors.size">@{{ errors.size[0] }}</small>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7" v-if="template.size != '4_7_large'">
                    <label class="col-sm-4 control-label">Orientation</label>
                    <div class="col-sm-8" v-bind:class="{ 'has-error': errors.orientation }">
                        <select class="form-control" v-on:change="resetSelectableFieldsStatement"  v-model="template.orientation">
                            <option value="vertical" selected>
                                Vertical
                            </option>
                            <option value="horizontal">Horizontal</option>
                        </select>
                        <small class="error" v-if="errors.orientation">@{{ errors.orientation[0] }}</small>
                    </div>
                </div>
            </div>
            <div class="row" v-if="template.orientation != 'horizontal' && !previewItem && template.size != '4_7_large'">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Hole</label>
                    <div class="col-sm-8 select-hole">
                        <div class="col-xs-6"><input
                                    v-model="template.hole" type="radio"
                                    value="1"> Yes
                        </div>
                        <div class="col-xs-6"><input
                                    v-model="template.hole" type="radio"
                                    value="0" checked> No
                        </div>
                    </div>
                </div>
            </div>
            <hr class="margin">
            <div class="row alert bg-danger" v-if="errors.fields">
                <small class="error">@{{ errors.fields[0] }}</small>
            </div>
            <!-- New Template Index Breeder vertical -->
            @include('layouts.cage-cards.partials.form._index_breeder')

            <!-- New Template Index Litter vertical -->
            @include('layouts.cage-cards.partials.form._index_litter')

            <!-- New Template Biz Breeder vertical -->
            @include('layouts.cage-cards.partials.form._business_breeder')

            <!-- New Template Biz Litter vertical -->
            @include('layouts.cage-cards.partials.form._business_litter')

            <!-- New Template Index Breeder horizontal-->
            @include('layouts.cage-cards.partials.form._index_breeder_horizontal')

            <!-- New Template Index Litter horizontal -->
            @include('layouts.cage-cards.partials.form._index_litter_horizontal')

            <!-- New Template Biz Breeder horizontal -->
            @include('layouts.cage-cards.partials.form._business_breeder_horizontal')

            <!-- New Template Biz Litter horizontal -->
            @include('layouts.cage-cards.partials.form._business_litter_horizontal')

            <!-- New Template Large Breeder -->
            @include('layouts.cage-cards.partials.form._large_breeder')

            <!-- New Template Large Litter -->
            @include('layouts.cage-cards.partials.form._large_litter')

            <!-- Preview Template Breeder Index -->
            @include('layouts.cage-cards.partials.preview.breeder.3_5_index')

            <!-- Preview Template Breeder Business -->
            @include('layouts.cage-cards.partials.preview.breeder.2_3_business')

            <!-- Preview Template Breeder Index -->
            @include('layouts.cage-cards.partials.preview.breeder.3_5_index_horizontal')

            <!-- Preview Template Breeder Business -->
            @include('layouts.cage-cards.partials.preview.breeder.2_3_business_horizontal')

            <!-- Preview Template Breeder Large -->
            @include('layouts.cage-cards.partials.preview.breeder.4_7_large')

            <!-- Preview Template Litter Index -->
            @include('layouts.cage-cards.partials.preview.litter.3_5_index')

            <!-- Preview Template Litter Business -->
            @include('layouts.cage-cards.partials.preview.litter.2_3_business')

            <!-- Preview Template Litter Index -->
            @include('layouts.cage-cards.partials.preview.litter.3_5_index_horizontal')

            <!-- Preview Template Litter Business -->
            @include('layouts.cage-cards.partials.preview.litter.2_3_business_horizontal')

            <!-- Preview Template Litter Large -->
            @include('layouts.cage-cards.partials.preview.litter.4_7_large')
        </form>
        <div class="row alert bg-danger" v-if="errors.no_entity">
            <small class="error">@{{ errors.no_entity }}</small>
        </div>
    </div>
    <div class="modal-footer bg-success">
        <button data-dismiss="modal" @click="closeModalHandler()" class="btn btn-default pull-left" type="button">Close</button>
        <div v-if="template.type && template.size">
            <button v-if="template.id" @click.prevent="printModal(template)" class="btn btn-primary" type="button"><i class="fa fa-print"></i> Print</button>
            <button @click="!template.id ? storeCageCardTemplate() : updateCageCardTemplate(template)" class="btn btn-success" type="button" v-bind:class="{ 'btn-info': template.id }">Save changes</button>
        </div>
    </div>
</template>