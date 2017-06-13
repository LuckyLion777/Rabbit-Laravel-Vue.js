<template id="kit-form-template">
    <div>
        <div class="modal" id="kit-form-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">
                            <span>Edit Kit</span>
                            <span class="loader1" v-if="loading"><i class="fa fa-spin fa-spinner"></i></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal row-paddings-compensation">

                            <!--- <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-4 control-label">Litter</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" disabled>
                                            <option selected>@{{ father().name  }}+@{{ mother().name  }} - @{{ litter.given_id }}</option>
                                        </select>
                                    </div>
                                </div>
                                
                            </div> --->

                            <div class="row">
                            	<div class="form-group col-sm-6 col-xs-7">
                                    <label class="col-sm-4 control-label">ID</label>
                                    <div class="col-sm-8"><input type="text" class="form-control" v-model="kit.given_id"></div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-7">
                                    <label class="col-sm-4 control-label">Color</label>
                                    <div class="col-sm-8"><input type="text" class="form-control" v-model="kit.color" v-el:color></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 col-xs-7">
                                    <label for="inputPassword3" class="col-xs-4 control-label">Sex</label>
                                    <div class="col-xs-8">
                                        <div class="icheck-group">
                                            <input class="js_icheck-kit-red"  type="radio" name="sex" value="doe" id="kit-sex-doe" v-model="kit.sex">
                                            <label for="kit-sex-doe" class="icheck-label"> Doe</label>
                                            <br />
                                            <input class="js_icheck-kit-blue" type="radio" name="sex" value="buck" id="kit-sex-buck" v-model="kit.sex">
                                            <label for="kit-sex-buck" class="icheck-label"> Buck</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Weight</label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div v-if="kit.weight.length > 0 && kit.weight_unit != 'Pound/Ounces'">
                                                <div class="col-sm-3 col-xs-6" v-for="(index, item) in kit_weight">
                                                    <p v-html="item.wdate" class="margin-bottom-none"></p>
                                                    <input type="number" v-model="kit.weight[index]" class="form-control" {{--v-bind:value="item.value"--}} lazy>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-xs-6" v-if="(!kit.weight || kit.weight.length < 20) && kit.weight_unit != 'Pound/Ounces'">
                                                <p class="margin-bottom-none">&nbsp;</p>
                                                <input type="number" class="form-control" v-model="kit.new_weight">
                                            </div>



                                            <div v-if="kit.weight_unit == 'Pound/Ounces'" class="col-sm-5 col-xs-6" v-for="(index, item) in kit_weight">
                                                <p v-html="item.wdate" class="margin-bottom-none"></p>
                                                <lbs-oz-input :weight.sync="kit.weight[index]"></lbs-oz-input>
                                            </div>

                                            <div v-if="kit.weight_unit == 'Pound/Ounces'" class="col-sm-5 col-xs-6">
                                                <p class="margin-bottom-none">&nbsp;</p>
                                                <lbs-oz-input :model="kit" :weight.sync="kit.new_weight"></lbs-oz-input>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <image-upload :breeder.sync="kit"></image-upload>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <label class="col-sm-2 control-label" for="exampleInputFile">Notes</label>
                                    <div class="col-sm-10"><textarea v-model="kit.notes" rows="3" class="form-control"></textarea></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <button v-if="!kit.sold_at" @click.prevent="showSold(kit)" class="btn btn-success"
                                            type="button"><i class="fa fa-dollar"></i> Sold</button>
                                    <button v-if="kit.sold_at" @click.prevent="showUnsold(kit)" class="btn btn-default"
                                            type="button"><i class="fa fa-dollar"></i> Undo sold</button>
                                    <button @click.prevent="showDied(kit)" class="btn btn-danger" type="button"><i class="fa fa-heart-o"></i> Died</button>
                                    <button @click.prevent="makeBreeder(kit)" class="btn btn-info" type="button"><i class="fa fa-venus-mars"></i> Breeder</button>
                                     <a href="#/pedigree/kit/@{{ kit.id }}" class="btn btn-primary"><i class="fa fa-share-alt"></i> Pedigree</a>
                                    <br><br>

                                    <button v-if="!kit.archived" @click.prevent="showArchive(kit)" class="btn btn-default" type="button" title="Archive"><i class="fa fa-archive"></i></button>
                                    <button v-if="kit.archived" @click.prevent="unarchive(kit)" class="btn btn-default" type="button" title="Unarchive"><i class="fa fa-expand"></i></button>

                                    <button @click.prevent="showDelete(kit)" class="btn btn-default" type="button" title="Delete"><i class="fa fa-trash"></i></button>

                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer bg-success">
                        <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                        <button class="btn btn-success" @click="saveKit(kit)" type="button">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        @include('layouts.archive-delete', ['unique' => '-kit'])
        @include('layouts.died', ['unique' => '-kit'])
    </div>
</template>
