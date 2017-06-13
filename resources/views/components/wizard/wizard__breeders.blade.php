<template id="wizard__breeders-template" xmlns="http://www.w3.org/1999/html" xmlns:v-on="http://www.w3.org/1999/xhtml">

    <div id="wizard__breeders">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="box box-solid bg-green-gradient">
                    <div class="box-body"><h2 class="text-center"><img src="img/logo-tiny.png"><br>Hutch Breeders
                        </h2>

                        <h4 class="text-center">Breeders are the production rabbits in your rabbitry</h4>
                        <p class="text-center">Add your breeders to Hutch to begin tracking their performance<br>
                        </p>
                        <a href="#" class="btn btn-success btn-sm pull-right" v-link="{ path: '/'}">
                            Skip <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center" v-show="!mode">
        	<label for="import-html" class="btn btn-info btn-lg"><i class="fa fa-upload"></i> Evans Pedigree</label>
        	<input @change="parseFile" type="file" id="import-html" style="opacity:0.01; position: fixed; top: -500px">
           <br /><br />
            <label for="import-file" class="btn btn-info btn-lg"><i class="fa fa-file"></i> CSV Import</label>
            <br /><a href="example.csv">Example File</a>
            <input @change="parseFile" type="file" id="import-file" style="opacity:0.01; position: fixed; top: -500px">
            <br /><br />
            <button @click="beginManual" type="button" class="btn btn-primary btn-lg"><i class="fa fa-hand-paper-o"></i> Enter Manually</button>
            
            
        </div>
        <template v-if="mode == 'manual'">
        	<div class="row">
            	<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                	<div class="box box-solid bg-light-blue-gradient">
                    	<div class="box-header text-center"><h3 class="box-title">How many Breeders do you have?</h3>
                    	</div>
                    	<div class="box-body">

	
                        	<div class="col-md-6"><p>You can schedule breeding events, track litters, add income/expense
                                items, and generate pedigrees for Breeders. This tool allows you to create multiple
                                records at a time.</p>

                        	</div>
                        	<div class="form-group col-md-6">
                            	<label for="">Number of Breeders to add right now</label>
                            	<input name="num_breeders" v-model.lazy="breedersNumber" class="form-control" type="text">
                        	</div>
                    	</div>

                	</div>
            	</div>
        	</div>
        </template>
        <template v-if="mode">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="box box-solid box-primary">
                    <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-check-circle"></i>
                            Select Breeder Fields</h3></div>
                    <div class="box-body">


                        <div class="row">
                            <div class="col-md-6"><p>Select the fields you want to add to your Breeders right now.
                                    You can fill in missing fields at a later time in Breeder Profiles.</p>
                                <p>Gather this information for all of your breeders. </p>
                            </div>

                            <div class="col-sm-3 col-xs-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="fields.tattoo"> Tattoo/ID </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="fields.cage">
                                        Cage
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="fields.color">
                                        Color
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="fields.breed">
                                        Breed
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.weight">
                                        Weight
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.photo">
                                        Photo
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.date_of_birth">
                                        Date of Birth
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.aquired">
                                        Date Aquired
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.registration_number">
                                        Registration #
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.champion_number">
                                        Champion Number
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.legs">
                                        Legs
                                    </label>
                                </div>
								<div class="checkbox">
                                	<label>
                                    	<input checked="" type="checkbox" v-model="fields.status">
                                    	Status
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input checked="" type="checkbox" v-model="fields.status_date">
                                        Status Date
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid box-primary">
                    <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-venus-mars"></i> Enter
                            Multiple Breeders</h3></div>
                    <div class="box-body">


                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6 text-center">
                                <a href="#guide" data-toggle="modal" data-target="#wizard-breeder-modal" role="button" class="btn btn-info btn-lg"><i
                                            class="fa fa-commenting"></i> Add Breeders with Guide </a><br><br>
                                <p class="text-center">Or enter your Breeder information in the rows below. </p>
                                <p class="text-center">To add a new row, click the "Add New" button. Empty rows will
                                    be ignored.<br><br></p></div>
                        </div>

                        <div class="row row-horizon pedigree">
                            <table class="table table-striped text-center">
                                <thead>
                                <tr>
                                    <th>Sex</th>
                                    <th v-if="fields.name">Name</th>
                                    <th v-if="fields.tattoo">Tattoo/ID</th>
                                    <th v-if="fields.cage">Cage</th>
                                    <th v-if="fields.color">Color</th>
                                    <th v-if="fields.breed">Breed</th>
                                    <th v-if="fields.weight">Weight</th>
                                    <th v-if="fields.date_of_birth" style="min-width:150px">DoB</th>
                                    <th v-if="fields.aquired" style="min-width:150px">Aquired</th>
                                    <th v-if="fields.registration_number">Reg #</th>
                                    <th v-if="fields.champion_number">Champ #</th>
                                    <th v-if="fields.legs">Legs</th>
                                    <th v-if="fields.status">Status</td>
                                    <th v-if="fields.status_date">Status Date</td>
                                    <th v-if="fields.photo">Photo</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="breeder in breeders" is="breeder" :breeder="breeder" :fields="fields" :user="user" :autocomplete="autocomplete"></tr>
                                </tbody>

                            </table>
                        </div>
                        <button class="btn btn-submit btn-default pull-left" data-toggle="modal" href="#new_chain" @click.prevent="addRow">
                            <i class="fa fa-plus"></i> Add New Row
                        </button>

                    </div>

                </div>
            </div>
        </div>
        <div class="row text-center">

            <button type="button" class="btn btn-success btn-lg" @click.prevent="saveBreeders">
                <i v-if="loading" class="fa fa-spinner fa-pulse fa-fw"></i>
                <span v-if="!loading">
                    Save Breeders <i class="fa fa-arrow-circle-right"></i>
                </span>
            </button>
            <br><br>
            <button type="button" class="btn btn-default btn-sm" v-link="{ path: '/'}"> Skip <i class="fa fa-chevron-right"></i></button>
            <br><br><br><br>
        </div>
        </template>
    </div>

    <div class="modal fade" id="wizard-breeder-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div  class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">x</span></button>
                    <h4 class="modal-title">
                        <span>New</span> Breeder  @{{ current+1 }}/@{{ breedersNumber }}
                    </h4>
                </div>
                <div class="modal-body">
                    <div v-for="(index, breeder) in breeders">
                        <breeder-form v-if="index == current" :breeder="breeder" :user="user" :fields="fields" :autocomplete="autocomplete"></breeder-form>
                    </div>


                </div>
                <div class="modal-footer bg-success">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-default" @click.prevent="previousBreeder">
                        <i class="fa fa-fw fa-arrow-circle-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-success btn-lg" @click.prevent="nextBreeder">Next <i
                                class="fa fa-arrow-circle-right"></i></button>

                </div>
            </div>
        </div>
    </div>

</template>

<template id="wizard__breeder-template">
    <tr>
        <td>
            <div class="btn-group">
                <button type="button" class="btn"
                        :class="{ 'btn-default': breeder.sex != 'doe', 'bg-maroon': breeder.sex == 'doe'}"
                        @click.prevent="setSex('doe')"><strong><i
                                class="fa fa-venus"></i></strong></button>
                <button type="button" class="btn"
                        :class="{ 'btn-default': breeder.sex != 'buck', 'bg-aqua': breeder.sex == 'buck'}"
                        @click.prevent="setSex('buck')"><strong><i
                                class="fa fa-mars"></i></strong></button>
                <input type="hidden" v-model="breeder.father_name" v-el:father_name/>
                <input type="hidden" v-model="breeder.mother_name" v-el:mother_name/>

            </div>
        </td>
        <td v-if="fields.name"><input class="form-control"  placeholder="Rabbit Name"
                   type="text" v-model="breeder.name" v-el:name></td>
        <td v-if="fields.tattoo"><input class="form-control" placeholder="1234" type="text"
                   v-model="breeder.tattoo" v-el:tattoo></td>
        <td v-if="fields.cage"><input class="form-control" placeholder="12" type="text"
                   v-model.lazy="breeder.cage" v-el:cage></td>
        <td v-if="fields.color"><input class="form-control" placeholder="White" type="text"
                   v-model="breeder.color" v-el:color></td>
        <td v-if="fields.breed"><input class="form-control" placeholder="New Zealand"
                   type="text" v-model="breeder.breed" v-el:breed></td>
        <td v-if="fields.weight">

                <input v-if="user.general_weight_units != 'Pound/Ounces'" type="number" v-model="breeder.weight" placeholder="Enter ..."
                       class="form-control"  min="0" step=".1">
                <lbs-oz-input v-if="user.general_weight_units == 'Pound/Ounces'" :model="breeder" :weight.sync="breeder.weight"></lbs-oz-input>

        </td>
        <td v-if="fields.date_of_birth">
            <div class="input-group date" v-datepicker="breeder.date_of_birth">
                <input type="text" class="form-control">
                <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
            </div>
        </td>
        <td v-if="fields.aquired">
            <div class="input-group date" v-datepicker="breeder.aquired">
                <input type="text" class="form-control">
                <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
            </div>
        </td>

        <td v-if="fields.registration_number"><input class="form-control" id="reg1" placeholder="1234" type="text"
                   v-model="breeder.registration_number"></td>
        <td v-if="fields.champion_number"><input class="form-control" id="champ1" placeholder="12" type="text"
                   v-model="breeder.champion_number"></td>
        <td v-if="fields.legs"><input class="form-control" id="legs1" placeholder="4" type="text"
                   v-model="breeder.legs"></td>
        <td v-if="fields.status">
        	<select class="form-control" v-model="breeder.status">
         		<option value="">Choose...</option>
            	<option value="archived">Archived</option>
            	<option value="died">Died</option>
            	<option value="butchered">Butchered</option>
            	<option value="sold">Sold</option>
            </select>
        </td>
        <td v-if="fields.status_date">
            <div v-if="breeder.status == 'died' || breeder.status == 'butchered' || breeder.status == 'sold'" class="input-group date" v-datepicker="breeder.status_date">
                <input type="text" class="form-control">
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-th"></i>
                </span>
            </div>
        </td>
        <td v-if="fields.photo" ><image-upload :breeder="breeder" cloud_settings_name="upload_breeders" v-if="fields.photo"></image-upload>
        </td>

    </tr>
</template>


<template id="wizard__breeder-form-template">
    <form v-if="breeder" id="wizard-breeder-form" class="form-horizontal row-paddings-compensation">
        <div class="row">


            <div class="form-group col-sm-6 col-xs-7">
                <label class="col-sm-4 control-label">Name</label>
                <div class="col-sm-8">
                    <input type="text" v-model="breeder.name"
                           v-el:name
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7" v-if="fields.tattoo">
                <label class="col-sm-4 control-label">ID</label>
                <div class="col-sm-8">
                    <input type="text" v-model="breeder.tattoo"
                           v-el:tattoo
                           id="breeder-tattoo" placeholder="Enter ..."
                    class="form-control typeahead">
                </div>
            </div>

            <div class="form-group col-sm-6 col-xs-7" v-if="fields.cage">
                <label class="col-sm-4 control-label">Cage</label>
                <div class="col-sm-8">
                    <input type="text" v-model="breeder.cage"
                           v-el:cage
                           placeholder="Enter ..." class="form-control typeahead">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7" v-if="fields.color">
                <label class="col-sm-4 control-label">Color</label>
                <div class="col-sm-8">
                    <input type="text" v-model="breeder.color"
                           id="breeder-color"
                           placeholder="Enter ..."
                           v-el:color
                           class="form-control typeahead">
                </div>
            </div>

            <div class="form-group col-sm-6 col-xs-7" v-if="fields.breed">
                <label class="col-sm-4 control-label">Breed</label>
                <div class="col-sm-8">
                    <input type="text" v-model="breeder.breed"
                           v-el:breed
                           id="breeder-breed" class="form-control typeahead" placeholder="Enter ...">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7" v-if="fields.weight">
                <label class="col-sm-4 control-label">Weight</label>
                <div v-if="user.general_weight_units != 'Pound/Ounces'" class="col-sm-8">
                    <input type="number" v-model="breeder.weight" placeholder="Enter ..."
                           class="form-control"  min="0" step=".1">
                </div>
                <div v-if="user.general_weight_units == 'Pound/Ounces'" class="col-sm-8">
                    <lbs-oz-input :model="breeder" :weight.sync="breeder.weight"></lbs-oz-input>
                </div>
            </div>

            <div class="form-group col-xs-7 col-sm-6" v-if="fields.date_of_birth">
                <label class="col-sm-4 control-label">Born</label>
                <div class="col-sm-8">
                    <div class="input-group date" v-datepicker="breeder.date_of_birth" container="#wizard-breeder-form">
                        <input type="text" class="form-control">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
                    </div>
                </div>
            </div>

            <div class="form-group col-xs-7 col-sm-6" v-if="fields.aquired">
                <label class="col-sm-4 control-label">Aquired</label>
                <div class="col-sm-8">
                    <div class="input-group date" v-datepicker="breeder.aquired" container="#wizard-breeder-form">
                        <input type="text" class="form-control">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i></span>
                    </div>
                </div>
            </div>


            <div class="form-group col-sm-6 col-xs-7" v-if="fields.registration_number">
                <label class="col-sm-4 control-label">Reg #</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" v-model="breeder.registration_number" id="pedigree-registration_number" class="form-control">

                    </div>
                </div>
            </div>

            <div class="form-group col-sm-6 col-xs-7" v-if="fields.champion_number">
                <label class="col-sm-4 control-label">Champ #</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" v-model="breeder.champion_number" id="pedigree-champion_number" class="form-control">

                    </div>
                </div>
            </div>
			
			<div class="form-group col-sm-6 col-xs-7" v-if="fields.status">
				<label class="col-sm-4 control-label">Status</label>
				<div class="col-sm-8">
					<select class="form-control" v-model="breeder.status" id="pedigree-status">
         				<option value="">Choose...</option>
            			<option value="archived">Archived</option>
            			<option value="died">Died</option>
            			<option value="butchered">Butchered</option>
            			<option value="sold">Sold</option>
            		</select>
				</div>
			</div>
			
			<div class="form-group col-sm-6 col-xs-7" v-if="fields.status_date && (breeder.status == 'died' || breeder.status == 'butchered' || breeder.status == 'sold')">
				<label class="col-sm-4 control-label" v-if="breeder.status == 'died' || breeder.status == 'butchered' || breeder.status == 'sold'">Status Date</label>
				<div class="col-sm-8">
					<div v-if="breeder.status == 'died' || breeder.status == 'butchered' || breeder.status == 'sold'" class="input-group date" v-datepicker="breeder.status_date">
                		<input type="text" class="form-control">
                		<span class="input-group-addon">
                    		<i class="glyphicon glyphicon-th"></i>
                		</span>
            		</div>
				</div>
			</div>
			
            <div class="form-group col-sm-6 col-xs-7" v-if="fields.legs">
                <label class="col-sm-4 control-label">Legs</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" v-model="breeder.legs" id="pedigree-legs" class="form-control">

                    </div>
                </div>
            </div>

            <image-upload :breeder.sync="breeder" cloud_settings_name="upload_breeders" v-if="fields.photo"></image-upload>

            <div class="form-group col-sm-6 col-xs-7">
                <label class="col-xs-4 control-label">Sex</label>
                <div class="col-xs-8">
                    <sex-select :model.sync="breeder"></sex-select>
                </div>
            </div>

        </div>

    </form>
</template>
