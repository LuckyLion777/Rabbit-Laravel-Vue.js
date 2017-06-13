<template id="breeder-butcher-template">
    <div>
        <div class="modal-header bg-red">
            <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">Butcher Breeder</h4>
        </div>
        <div class="modal-body">
            <form class="form-horizontal row-paddings-compensation">
                <div class="row">
                    <div class="form-group col-xs-7 col-sm-6 col-xs-12">
                        <label class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-8">
                            <div id="datepick" class="input-group date" v-datepicker="date" container="#breeder-butcher-modal">
                                <input type="text" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th class="col-xs-1"></th>
                                <th class="col-xs-2">Name</th>
                                <th class="col-xs-2"></th>
                                <th class="col-xs-1">Color</th>
                                <th class="text-center">Weight</th>
                            </tr>
                            <tr v-bind:class="getGenderClass(breeder.sex)">
                                <td></td>
                                <td>@{{ breeder.name }}</td>
                                <td>
                                    <img class="img-responsive img-circle" v-bind:src="breeder.image.path">
                                </td>
                                <td>@{{ breeder.color }}</td>
                                <td>
                                    <input type="text" v-if="breeder.weight_unit != 'Pound/Ounces'" data-mobile-type="number" v-model="breeder.weight" class="form-control js_only-numbers">

                                    <lbs-oz-input v-if="breeder.weight_unit == 'Pound/Ounces'" :model="breeder" :weight.sync="breeder.weight"></lbs-oz-input>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer bg-danger">
            <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close
            </button>
            <button class="btn btn-danger" type="button" @click="sendToButcher">Save changes</button>
        </div>
    </div>
</template>
