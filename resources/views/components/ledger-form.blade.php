<template id="ledger-form-template">

    <div class="modal-header bg-success" v-bind:class="{ 'bg-info': entry.id }">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">×</span></button>
        <h4 class="modal-title">
            <span v-if="entry.id">Edit</span>
            <span v-if="!entry.id">New</span> Ledger Item
            <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
        </h4>
    </div>

    <div class="modal-body">
        <form class="form-horizontal row-paddings-compensation">
            <div class="row">
                <div class="form-group col-xs-7 col-sm-6" :class="{ 'has-error': errors.date }">
                    <label class="col-sm-4 control-label" for="ledger-date">Date</label>
                    <div class="col-sm-8">
                        <div class="input-group date" v-datepicker="entry.date" id="ledger-date" container="#ledger-form">
                            <input type="text" class="form-control">
                            <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
                        </div>
                        <small class="error" v-if="errors.date">@{{ errors.date[0] }}</small>
                    </div>
                </div>

                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.name }">
                    <label class="col-sm-4 control-label" for="entry-name">Name</label>
                    <div class="col-sm-8">
                        <input placeholder="Enter ..." class="form-control" type="text" id="entry-name" v-model="entry.name">
                        <small class="error" v-if="errors.name">@{{ errors.name[0] }}</small>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.category_id }">
                    <label class="col-sm-4 control-label" for="ledger-category">Category</label>
                    <div class="col-sm-8">
                        <select v-if="!associated_type" class="form-control" id="ledger-category" v-model="entry.category_id">
                            <option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                            <option value="">Other...</option>
                        </select>
                        <p v-else class="form-control-static">@{{ category.name }}</p>
                        <small class="error" v-if="errors.category_id">@{{ errors.category_id[0] }}</small>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.associated_id }" v-if="associated">
                    <label class="col-sm-4 control-label" for="ledger-association">@{{ category.name }}</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <select v-if="!associated_type" class="form-control" id="ledger-association" v-model="entry.associated_id">
                                <option value="">Choose...</option>
                                <option v-if="category.special != 'breeder'" v-for="association in associations" :value="association.id"> @{{ representAssociation(association) }}</option>
                                <option v-if="category.special == 'breeder'" v-for="association in associations | filterBy filterEntriesBreeders" :value="association.id"> @{{ representAssociation(association) }}</option>
                            </select>
                            <span v-if="category.special == 'breeder'" class="input-group-addon" title="With Archived">
                                <input type="checkbox" v-model="withArchivedBreeders">
                            </span>
                        </div>

                        <p v-else class="form-control-static">@{{ representAssociation(associated_record) }}</p>
                        <small class="error" v-if="errors.associated_id">@{{ errors.associated_id[0] }}</small>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7" :class="{ 'has-error': errors.category_name }" v-if="!entry.category_id">
                    <label class="col-sm-4 control-label" for="entry-category-name"><span class="sr-only">New category</span></label>
                    <div class="col-sm-8">
                        <input placeholder="Enter ..." class="form-control" type="text" id="entry-category-name" v-model="entry.category_name">
                        <small class="error" v-if="errors.category_name">@{{ errors.category_name[0] }}</small>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-xs-4 control-label">Type</label>
                    <div class="col-xs-8">
                        <div class="icheck-group">
                            <input class="js_icheck-ledger-green"  type="radio" name="debit" value="1" id="ledger-type-debit" v-model="entry.debit">
                            <label for="ledger-type-debit" class="icheck-label"> Income</label> <br />
                            <input class="js_icheck-ledger-red" type="radio" name="debit" value="0" id="ledger-type-credit" v-model="entry.debit">
                            <label for="ledger-type-credit" class="icheck-label"> Expense</label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.amount }">
                    <label class="col-sm-4 control-label" for="entry-amount">Amount</label>
                    <div class="col-sm-8">
                        <input placeholder="Enter ..." class="form-control" min="0" step="1" type="number" v-model="entry.amount" id="entry-amount">
                        <small class="error" v-if="errors.amount">@{{ errors.amount[0] }}</small>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-12">
                    <label class="col-sm-2 control-label">Notes</label>
                    <div class="col-sm-10">
                        <textarea placeholder="Descriptions" rows="3" class="form-control" v-model="entry.description"></textarea>
                    </div>
                </div>
            </div>

            <div v-if="entry.id" class="row">
                <div class="col-sm-12 text-center">
                    <button v-if="!entry.archived_at" @click.prevent="showArchive" class="btn btn-default" type="button" title="Archive"><i class="fa fa-archive"></i></button>
                    <button @click.prevent="showDelete(entry)" class="btn btn-default" type="button" title="Delete"><i class="fa fa-trash"></i></button>
                </div>
            </div>

        </form>

    </div>
    <div class="modal-footer bg-success" v-bind:class="{ 'bg-info': entry.id }">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button v-if="entry.id" type="button" @click.prevent="send" class="btn btn-info">Save changes</button>
        <button v-if="!entry.id" type="button" @click.prevent="send" class="btn btn-success">Submit</button>
    </div>
</template>
