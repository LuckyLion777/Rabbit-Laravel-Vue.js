<template id="cage-cards-print-template">
    <div class="modal-header bg-info">
        <button @click="closeModalHandler" aria-label="Close" data-dismiss="modal" class="close"
        type="button"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Print Cage Cards</h4>
    </div>
    <div class="modal-body">
        <form class="form-horizontal row-paddings-compensation">
            <div class="row">
                <div class="form-group col-xs-7 col-sm-6">
                    <label class="col-sm-4 control-label">Template</label>
                    <div class="col-sm-8">
                        <select class="form-control" v-on:change="changeTemplate">
                            <option value="" disabled selected hidden>Choose</option>
                            <option v-bind:selected="templateItem.id == template.id" v-for="templateItem in alltemplates" value="@{{ templateItem.id }}">@{{ templateItem.name }}</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-xs-7 col-sm-6" v-if="template.type == 'breeder' && !profileid">
                    <label class="col-sm-4 control-label">Show</label>
                    <div class="col-sm-8">
                        <select v-model="breedersFilter" class="form-control" v-on:change="getListOfBreeders">
                            <option value="all">All</option>
                            <option value="buck">Bucks</option>
                            <option value="doe">Does</option>
                            <option value="categories">Categories</option>
                        </select>
                        <select v-if="breedersFilter == 'categories'" v-on:change="chooseBreedersCategory" class="form-control">
                            <option v-for="breederCategory in breederCategories" value="@{{ breederCategory.id }}">@{{ breederCategory.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-if="template.type == 'breeder' && !profileid">
                <div class="col-xs-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-1"><input v-bind:checked="breeders.length == selectedBreeders.length && selectedBreeders.length" id="print-breeder-all" type="checkbox" @click="selectAllPrintModels('breeder')"></th>
                            <th class="col-xs-1"></th>
                            <th class="col-xs-4">Name</th>
                            <th class="col-xs-4">ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="breeder in breeders">
                            <td>
                                <input v-bind:checked="selectedBreeders.indexOf(breeder.id) > -1" id="print-breeder-@{{ breeder.id }}" @click="selectPrintModel('breeder', breeder.id)" value="@{{ breeder.id }}" class="" type="checkbox">
                            </td>
                            <td class="col-xs-1">
                                <img class="img-responsive img-circle" v-bind:src="breeder.image.path">
                            </td>
                            <td>@{{ trimStrTo(breeder.name, 30) }}</td>
                            <td>@{{ breeder.id }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </form>
        <form class="form-horizontal row-paddings-compensation">
            <div class="row" v-if="template.type == 'litter' && !profileid">
                <div class="col-xs-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-1"><input v-bind:checked="litters.length == selectedLitters.length && selectedLitters.length" id="print-litter-all" type="checkbox" @click="selectAllPrintModels('litter')"></th>
                            <th class="col-xs-1"></th>
                            <th class="col-xs-1"></th>
                            <th class="col-xs-4">Parents</th>
                            <th class="col-xs-4">Litter ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="litter in litters">
                            <td><input v-bind:checked="selectedLitters.indexOf(litter.id) > -1" id="print-litter-@{{ litter.id }}" @click="selectPrintModel('litter', litter.id)" value="@{{ litter.id }}" class=""
                                type="checkbox"></td>
                            <td class="col-xs-1">
                                <img class="img-responsive img-circle"
                                     v-bind:src="litter.father ? litter.father.image.path : ''">
                            </td>
                            <td class="col-xs-1">
                                <img class="img-responsive img-circle"
                                     v-bind:src="litter.mother ? litter.mother.image.path : ''">
                            </td>

                            <td>@{{ getLitterParentsName(litter) }} </td>
                            <td>@{{ litter.given_id }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row alert bg-danger" v-if="errors.length">
                <small class="error">@{{ errors[0] }}</small>
            </div>
        </form>
    </div>
    <div class="modal-footer bg-info">
        <button @click="closeModalHandler" type="button" class="btn btn-default pull-left"
        data-dismiss="modal">Close
        </button>
        <a v-if="isPrintBtnVisible()"  href="{{ url("admin/cage-cards/print-batch") }}/@{{ template.id }}/@{{ getSelectedModelsIdsStr() }}" type="button" class="btn btn-primary">Print Batch</a>
    </div>
</template>