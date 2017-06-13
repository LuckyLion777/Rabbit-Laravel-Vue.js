<template id="copy-pedigree-form-template">
    <div class="modal modal-info" id="copy-pedigree-form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="text-center">Do you want to import data for @{{ name }}?</h3>
                            <p class="lead text-center">
                                Select @{{ type }} to copy, and current pedigree data will be replaced.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5 text-right">
                            <label class="control-label" for="copy-pedigree-from">Copy from</label>
                        </div>
                        <div class="col-xs-7">
                            <div class="input-group">
                                <select id="copy-pedigree-from" class="form-control" v-model="copyId">
                                    <option value="">Choose</option>
                                    <option v-for="option in options" :value="option.id">
                                        @{{ option.title }}
                                    </option>
                                </select>
                                <span class="input-group-addon" title="With Archived">
                                    <input type="checkbox" v-model="withArchived">
                                </span>
                            </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-xs-5 text-right">
                            <label class="control-label" for="copy-pedigree-line">Which line</label>
                        </div>
                        <div class="col-xs-7">
                            <div class="input-group">
                                <select id="copy-pedigree-line" class="form-control" v-model="line">
                                    <option selected value="both">Both</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-outline" type="button" @click="copy" :disabled="!copyId">
                                <i class="fa fa-check"></i> Import Data
                            </button>
                            <button type="button" class="btn btn-outline" data-dismiss="modal">
                                <i class="fa fa-close"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
