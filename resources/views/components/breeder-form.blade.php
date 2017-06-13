<template id="breeder-form-template">

    <div class="modal-header bg-success" v-bind:class="{ 'bg-info': breeder.id }">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">×</span></button>
        <h4 class="modal-title">
            <span v-if="breeder.id">Edit</span>
            <span v-if="!breeder.id">New</span> Breeder
            <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
        </h4>
    </div>
    <div class="modal-body">
        <form class="form-horizontal row-paddings-compensation">
            <input v-if="breeder.id !=0" name="_method" v-model="breeder._method" type="hidden" value="PUT">

            <div class="row">
                <div class="form-group col-xs-7 col-sm-6">
                    <div>
                        <label for="breeder-category" class="col-sm-4 control-label">Category</label>
                        <div class="col-sm-8" v-bind:class="{ 'has-error': errors.category_i }">
                            <select id="breeder-category" class="form-control" v-model="breeder.category_id">
                                <option v-for="category in categories" value="@{{ category.id }}">@{{ category.name }}</option>
                                <option value="">Other...</option>
                            </select>
                            <small class="error" v-if="errors.category_id">@{{ errors.category_id[0] }}</small>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div style="margin-top:15px" v-show="!breeder.category_id">
                        <label class="col-sm-4 control-label" for="breeder-category-name"><span class="sr-only">New category</span></label>
                        <div class="col-sm-8">
                            <input placeholder="Enter ..." class="form-control" type="text" id="breeder-category-name" v-model="breeder.category_name">
                            <small class="error" v-if="errors.category_name">@{{ errors.category_name[0] }}</small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.prefix }">
                    <label class="col-sm-4 control-label">Prefix</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.prefix" placeholder="Enter ..." class="form-control">
                        <small class="error" v-if="errors.prefix">@{{ errors.prefix[0] }}</small>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.name }">
                    <label class="col-sm-4 control-label">Name</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.name" placeholder="Enter ..." class="form-control">
                        <small class="error" v-if="errors.name">@{{ errors.name[0] }}</small>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-warning': warnings.tattoo }">
                    <label class="col-sm-4 control-label">ID</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.tattoo" id="breeder-tattoo" @keyup="checkDoubledId | debounce 300" placeholder="Enter ..."
                        class="form-control typeahead">
                        <small class="warnings" v-if="warnings.tattoo">@{{ warnings.tattoo[0] }}</small>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Cage</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.cage" id="breeder-cage" placeholder="Enter ..." class="form-control typeahead">
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Color</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.color" id="breeder-color" placeholder="Enter ..." class="form-control typeahead">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Breed</label>
                    <div class="col-sm-8">
                        <input type="text" v-model="breeder.breed" id="breeder-breed" class="form-control typeahead" placeholder="Enter ...">
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-xs-4 control-label">Sex</label>
                    <div class="col-xs-8">
                        <sex-select :model.sync="breeder"></sex-select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label">Weight</label>
                    <div v-if="weight_units != 'Pound/Ounces'" class="col-sm-8">
                        <input type="number" v-model="breeder.weight" placeholder="Enter ..."
                               class="form-control"  min="0" step=".1">
                    </div>
                    <div v-if="weight_units == 'Pound/Ounces'" class="col-sm-8">
                        <lbs-oz-input :model="breeder" :weight.sync="breeder.weight"></lbs-oz-input>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="form-group col-xs-7 col-sm-6">
                    <label class="col-sm-4 control-label">Born</label>
                    <div class="col-sm-8">
                        <div class="input-group date" v-datepicker="breeder.date_of_birth" container="#breeder-form">
                            <input type="text" class="form-control">
                            <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group col-xs-7 col-sm-6">
                    <label class="col-sm-4 control-label">Aquired</label>
                    <div class="col-sm-8">
                        <div class="input-group date" v-datepicker="breeder.aquired" container="#breeder-form">
                            <input type="text" class="form-control">
                            <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label" for="buck_select">Father</label>
                    <div class="col-sm-8">
                        {{--<div class="input-group">--}}
                        {{--<select id="buck_select" class="form-control" v-model="breeder.father_id">--}}
                        {{--<option value="0">Choose</option>--}}
                        {{--<option v-for="buck in bucks | filterBy filterFatherBreeders" v-bind:value="buck.id">--}}
                        {{--@{{ buck.name }}: @{{ buck.tattoo }}--}}
                        {{--</option>--}}
                        {{--<option value="-1">Other ...</option>--}}
                        {{--</select>--}}
                        {{--<span class="input-group-addon" title="With Archived">--}}
                        {{--<input type="checkbox" v-model="withArchivedFather">--}}
                        {{--</span>--}}
                        {{--</div>--}}
                        {{--<div v-if="breeder.father_id == -1">--}}
                        {{--<div class="clearfix">--}}
                        {{--<input style="width: 60%;float: left;" type="text" v-model="newBuck.name" placeholder="name" class="form-control">--}}
                        {{--<input style="width: 40%;float: left;" type="text" v-model="newBuck.tattoo" placeholder="ID" class="form-control">--}}
                        {{--</div>--}}
                        {{--<a href="#" @click.prevent="addNewBuck" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add father buck</a>--}}
                        {{--</div>--}}
                        <div class="input-group">
                            <input type="text" id="buck_select" class="form-control" autocomplete="off"
                                   v-model="father_name"
                                   @focus.prevent="father_input_focused = true">
                            <input type="hidden" class="form-control" v-model="breeder.father_id">
                            <ul class="dropdown-menu" style="display: block !important;" v-if="father_input_focused && breeder.father_id !== -1">
                                <li v-for="buck in bucks | filterBy filterFatherBreeders | filterBy father_name in 'name'">
                                    <a href="" @click.prevent="breeder.father_id = buck.id">@{{ buck.name }} @{{ buck.tatto }}</a>
                                </li>
                                <li>
                                    <a href="" @click.prevent="breeder.father_id = -1">Other</a>
                                </li>
                            </ul>
                            <div v-if="breeder.father_id == -1">
                                <div class="clearfix">
                                    <input style="width: 60%;float: left;" type="text" v-model="newBuck.name" placeholder="name" class="form-control">
                                    <input style="width: 40%;float: left;" type="text" v-model="newBuck.tattoo" placeholder="ID" class="form-control">
                                </div>
                                <a href="#" @click.prevent="addNewBuck" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add father buck</a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-group col-sm-6 col-xs-7">
                    <label class="col-sm-4 control-label" for="doe_select">Mother</label>
                    <div class="col-sm-8">
                        {{--<div class="input-group">--}}
                        {{--<select class="form-control" v-model="breeder.mother_id">--}}
                        {{--<option value="0">Choose</option>--}}
                        {{--<option v-for="doe in does | filterBy filterMotherBreeders" v-bind:value="doe.id">--}}
                        {{--@{{ doe.name }}: @{{ doe.tattoo }}--}}
                        {{--</option>--}}
                        {{--<option value="-1">Other ...</option>--}}
                        {{--</select>--}}
                        {{--<span class="input-group-addon" title="With Archived">--}}
                        {{--<input type="checkbox" v-model="withArchivedMother">--}}
                        {{--</span>--}}
                        {{--</div>--}}
                        {{--<div v-if="breeder.mother_id == -1">--}}
                        {{--<div class="clearfix">--}}
                        {{--<input style="width: 60%;float: left;" type="text" v-model="newDoe.name" placeholder="name" class="form-control">--}}
                        {{--<input style="width: 40%;float: left;" type="text" v-model="newDoe.tattoo" placeholder="ID" class="form-control">--}}
                        {{--</div>--}}
                        {{--<a href="#" @click.prevent="addNewDoe" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add mother doe</a>--}}
                        {{--</div>--}}

                        <div class="input-group">
                            <input type="text" id="doe_select" class="form-control" autocomplete="off"
                                   v-model="mother_name"
                                   @focus.prevent="mother_input_focused = true">
                            <input type="hidden" class="form-control" v-model="breeder.mother_id">
                            <ul class="dropdown-menu" style="display: block !important;" v-if="mother_input_focused && breeder.mother_id !== -1">
                                <li v-for="doe in does | filterBy filterMotherBreeders | filterBy mother_name in 'name'">
                                    <a href="" @click.prevent="breeder.mother_id = doe.id">@{{ doe.name }} @{{ doe.tatto }}</a>
                                </li>
                                <li>
                                    <a href="" @click.prevent="breeder.mother_id = -1">Other</a>
                                </li>
                            </ul>
                            <div v-if="breeder.mother_id == -1">
                                <div class="clearfix">
                                    <input style="width: 60%;float: left;" type="text" v-model="newDoe.name" placeholder="name" class="form-control">
                                    <input style="width: 40%;float: left;" type="text" v-model="newDoe.tattoo" placeholder="ID" class="form-control">
                                </div>
                                <a href="#" @click.prevent="addNewDoe" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Add mother doe</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">

                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.registration_number }">
                    <label class="col-sm-4 control-label">Reg #</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" v-model="breeder.registration_number" id="pedigree-registration_number" class="form-control">

                        </div>
                        <small class="error" v-if="errors.registration_number">@{{ errors.registration_number[0] }}</small>
                    </div>
                </div>

                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.champion_number }">
                    <label class="col-sm-4 control-label">Champ #</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" v-model="breeder.champion_number" id="pedigree-champion_number" class="form-control">

                        </div>
                        <small class="error" v-if="errors.champion_number">@{{ errors.champion_number[0] }}</small>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6 col-xs-7" v-bind:class="{ 'has-error': errors.legs }">
                    <label class="col-sm-4 control-label">Legs</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" v-model="breeder.legs" id="pedigree-legs" class="form-control">

                        </div>
                        <small class="error" v-if="errors.legs">@{{ errors.legs[0] }}</small>
                    </div>
                </div>

            </div>

            <div class="row">
                <image-upload :breeder.sync="breeder"></image-upload>
            </div>

            {{--<div class="row">--}}
            {{--<div class="form-group col-sm-12">--}}
            {{--<label class="col-sm-2 control-label">Notes</label>--}}
            {{--<div class="col-sm-10">--}}
            {{--<textarea v-model="breeder.notes" placeholder="Descriptions" rows="3"--}}
            {{--class="form-control"></textarea>--}}
            {{--<ul class="list-group">--}}
            {{--<li class="list-group-item">Cras justo odio <i class="fa fa-trash"></i></li>--}}
            {{--<li class="list-group-item">Dapibus ac facilisis in</li>--}}
            {{--<li class="list-group-item">Morbi leo risus</li>--}}
            {{--<li class="list-group-item">Porta ac consectetur ac</li>--}}
            {{--<li class="list-group-item">Vestibulum at eros</li>--}}
            {{--<button class="list-group-item"><i class="fa fa-plus-circle"></i></button>--}}
            {{--</ul>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
        </form>

    </div>
    <div class="modal-footer bg-success" v-bind:class="{ 'bg-info': breeder.id }">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="button" @click="sendBreeder" class="btn btn-success" v-bind:class="{ 'btn-info': breeder.id }">Save changes</button>
    </div>

</template>
