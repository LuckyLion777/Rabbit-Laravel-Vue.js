<template id="litter-form-template">
    <div class="modal-header bg-success" v-bind:class="{ 'bg-info': litter.id }">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">
            <span v-if="!litter.id">New</span>
            <span v-if="litter.id">Edit</span>
            Litter
            <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
        </h4>
    </div>
    <div class="modal-body">
        <form class="form-horizontal row-paddings-compensation">

            <div class="row">
                <div class="form-group col-xs-7 col-sm-6" v-bind:class="{ 'has-error': errors.given_id }">
                    <label class="col-sm-4 control-label">Litter ID</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="litter.given_id" class="form-control" placeholder="Enter ...">
                        <small class="error" v-if="errors.given_id">@{{ errors.given_id[0] }}</small>
                    </div>
                </div>

                <div class="form-group col-xs-7 col-sm-6" v-bind:class="{ 'has-error': errors.kits_amount }">
                    <label class="col-sm-4 control-label"># Kits</label>
                    <div class="col-sm-8">
                        <input v-bind:disabled="litter.weighs > 0" type="text" v-model="litter.kits_amount" data-mobile-type="number" class="form-control" placeholder="Enter ...">
                        <small class="error" v-if="errors.kits_amount">@{{ errors.kits_amount[0] }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-7 col-sm-6">
                    <label class="col-sm-4 control-label">Buck</label>

                    <div class="col-sm-8">

                        <div class="input-group">
                            <select class="form-control" v-model="litter.father_id">
                                <option value="0" selected>Choose</option>
                                <option v-for="buck in bucks | filterBy filterFatherBreeders" v-bind:value="buck.id">
                                    @{{ buck.name }}: @{{ buck.tattoo }}
                                </option>
                                <option value="-1">Other ...</option>
                            </select>
                            <span class="input-group-addon" title="With Archived">
                                <input type="checkbox" v-model="withArchivedFather">
                            </span>
                        </div>
                        <div v-if="litter.father_id == -1">
                            <div class="clearfix">
                                <input style="width: 60%;float: left;" type="text" v-model="newBuck.name" placeholder="name" class="form-control">
                                <input style="width: 40%;float: left;" type="text" v-model="newBuck.tattoo" placeholder="ID" class="form-control">
                            </div>
                            <a href="#" @click.prevent="addNewBuck" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add father buck</a>
                        </div>
                    </div>
                </div>

                <div class="form-group col-xs-7 col-sm-6">
                    <label class="col-sm-4 control-label">Doe</label>

                    <div class="col-sm-8">
                        <div class="input-group">
                            <select class="form-control" v-model="litter.mother_id">
                                <option value="0" selected>Choose</option>
                                <option v-for="doe in does | filterBy filterMotherBreeders" v-bind:value="doe.id">
                                    @{{ doe.name }}: @{{ doe.tattoo }}
                                </option>
                                <option value="-1">Other ...</option>
                            </select>
                            <span class="input-group-addon" title="With Archived">
                                <input type="checkbox" v-model="withArchivedMother">
                            </span>
                        </div>
                        <div v-if="litter.mother_id == -1">
                            <div class="clearfix">
                                <input style="width: 60%;float: left;" type="text" v-model="newDoe.name" placeholder="name" class="form-control">
                                <input style="width: 40%;float: left;" type="text" v-model="newDoe.tattoo" placeholder="ID" class="form-control">
                            </div>
                            <a href="#" @click.prevent="addNewDoe" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add mother doe</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Bred</label>

                    <div class="col-sm-8">
                        <div id="datepick" class="input-group date" v-datepicker="litter.bred" container="#litter-form">
                            <input type="text" class="form-control"><span class="input-group-addon"><i
                                        class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Born</label>

                    <div class="col-sm-8">
                        <div id="datepick" class="input-group date" v-datepicker="litter.born" container="#litter-form">
                            <input type="text" class="form-control"><span class="input-group-addon"><i
                                        class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="row">--}}
                {{--<div class="form-group col-sm-12">--}}
                    {{--<label class="col-sm-2 control-label">Notes</label>--}}

                    {{--<div class="col-sm-10"><textarea v-model="litter.notes" class="form-control" rows="3" placeholder="Descriptions"></textarea>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="row" v-show="(!litter.butchered && litter.kitsForButchCount == 0 && litter.kitsButcheredCount != 0) || litter.butchered && litter.kitsForButchCount > 0">
                <div class="form-group col-sm-6 col-xs-7">
                    <div class="col-sm-4">
                        <label class="control-label">Butchered </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" v-model="butchered" v-el:litter_butchered>
                    </div>
                </div>
            </div>
            <div class="row" v-if="litter.butchered || (litter.kitsButcheredCount != 0 && litter.kitsForButchCount == 0)">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Date Butchered</label>

                    <div class="col-sm-8">
                        <div id="datepick" class="input-group date" v-datepicker="litter.butchered_date" container="#litter-form">
                            <input type="text" class="form-control"><span class="input-group-addon"><i
                                        class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer bg-success" v-bind:class="{ 'bg-info': litter.id }">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="button" @click="sendLitter" class="btn btn-success" v-bind:class="{ 'btn-info': litter.id }">Save changes</button>
    </div>
</template>