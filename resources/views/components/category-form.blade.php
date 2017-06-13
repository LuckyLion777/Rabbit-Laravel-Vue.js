<template id="category-form-template">

    <div class="modal-header bg-success" v-bind:class="{ 'bg-info': category.id }">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">×</span></button>
        <h4 class="modal-title">
            <span v-if="category.id">Edit</span>
            <span v-if="!category.id">New</span> Category
            <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
        </h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-sm-12" v-bind:class="{ 'has-error': errors.name }">
                <label class="col-sm-4 control-label">Name</label>
                <div class="col-sm-8">
                    <input id="category-name" placeholder="Enter ..." class="form-control" type="text" v-model="category.name">
                    <small class="error" v-if="errors.name">@{{ errors.name[0] }}</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-12">
                <label class="col-sm-4 control-label">Description</label>
                <div class="col-sm-8">
                    <textarea placeholder="Descriptions" rows="3" class="form-control" v-model="category.description"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-success" v-bind:class="{ 'bg-info': category.id }">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button v-if="category.id" type="button" @click.prevent="send" class="btn btn-info">Save changes</button>
        <button v-if="!category.id" type="button" @click.prevent="send" class="btn btn-success">Submit</button>
    </div>
</template>
