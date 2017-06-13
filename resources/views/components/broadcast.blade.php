<template id="broadcast-template">
    <div class="modal" id="broadcast-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Broadcast message</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal row-paddings-compensation">

                        <div class="form-group" :class="{ 'has-error': errors.title }">
                            <label class="col-sm-2 control-label" for="broadcast-title">Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="broadcast-title" v-model="title">
                                <small class="error" v-if="errors.title">@{{ errors.title[0] }}</small>
                            </div>
                        </div>
                        <div class="form-group" :class="{ 'has-errors': errors.content }">
                            <label class="col-sm-2 control-label" for="broadcast-content">Content</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="broadcast-content" v-model="content" rows="3"></textarea>
                                <small class="error" v-if="errors.content">@{{ errors.content[0] }}</small>
                            </div>
                        </div>

                        <div class="form-group" :class="{ 'has-errors': errors.content }">
                            <div class="col-sm-offset-2 col-sm-10">
                                @{{{ content }}}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-success">
                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                    <button type="button" class="btn btn-success" v-show="existing" v-on:click="update">Update</button>
                    <button type="button" class="btn btn-primary" v-on:click="saveNew">@{{ existing ? 'Save as new' : 'Save' }}</button>
                    <button type="button" class="btn btn-danger" v-show="existing" v-on:click="delete">Delete</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</template>
