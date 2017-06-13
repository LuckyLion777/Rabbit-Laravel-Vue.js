<template id="wizard__ledgers-template" xmlns="http://www.w3.org/1999/html" xmlns:v-on="http://www.w3.org/1999/xhtml">
	<div id="wizard__ledgers">
		<div class="row" v-if="mode">
			<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        		<div class="box box-solid bg-green-gradient">
                	<div class="box-body">
                		<h2 class="text-center"><img src="img/logo-tiny.png"><br>Hutch Ledgers</h2>
                		<h4 class="text-center">Ledgers for your purchases</h4>
                        <p class="text-center">Add your ledgers to Hutch to begin tracking your cost<br>
                        </p>
                	</div>
            	</div>
            	
			</div>
		</div>

		<template v-if="mode">
            
   			<div class="row">
   				<div class="col-md-12">
        			<div class="box box-solid box-primary">
            			<div class="box-header text-center"><h3 class="box-title"><i class="fa fa-venus-mars"></i> Enter
                			Multiple Ledgers</h3>
              			</div>
               			<div class="box-body">

               				<div class="row">
                    			<div class="col-md-3">
                        		</div>
                        	
                  			</div>

                    		<div class="row row-horizon pedigree">
                    			<table class="table table-striped text-center">
                       				<thead>
                            			<tr>
                            				<th>Type</th>
                               				<th>Date</th>
                                   			<th>Name</th>
                                   			<th>Category</th>
                                   			<th>Association</th>
                                  			<th>Amount</th>
                                  			<th>Notes</th>
                             			</tr>
                          			</thead>
                             	
                           			<tbody>
                            			<tr v-for="ledger in ledgers" is="ledger" :ledger="ledger" :fields="fields" :user="user" :autocomplete="autocomplete"></tr>
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

   				<button type="button" class="btn btn-success btn-lg" @click.prevent="saveLedgers">
        			<i v-if="loading" class="fa fa-spinner fa-pulse fa-fw"></i>
           				<span v-if="!loading">
                			Save Ledgers <i class="fa fa-arrow-circle-right"></i>
              			</span>
      			</button>
       				<br><br>
			</div>
		</div>

   		<div class="modal fade" id="wizard-ledger-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    		<div class="modal-dialog" role="document">
      			<div class="modal-content">
           			<div  class="modal-header bg-success">
                		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    			aria-hidden="true">×</span></button>
                    		<h4 class="modal-title">
                        		<span>New</span> Ledger  @{{ current+1 }}/@{{ ledgersNumber }}
                        	</h4>
              		</div>
              		<div class="modal-body">
               			<div v-for="(index, ledger) in ledgers">
                   			<ledger-form v-if="index == current" :ledger="ledger" :user="user" :fields="fields" :autocomplete="autocomplete"></ledger-form>
                  		</div>

               		</div>
              		<div class="modal-footer bg-success">
               			<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close
               			</button>
                  		<button type="button" class="btn btn-default" @click.prevent="previousLedger">
                    		<i class="fa fa-fw fa-arrow-circle-left"></i>
                            	Previous
                   		</button>
                  		<button type="button" class="btn btn-success btn-lg" @click.prevent="nextLedger">Next 
                  			<i class="fa fa-arrow-circle-right"></i>
                  		</button>

             		</div>
           		</div>
        	</div>
		</div>

	</template>

	<template id="wizard__ledger-template">
        <tr>
        	<td>
            	<select class="form-control" v-model="ledger.debit">
            		<option value="1">Income</option>
            		<option value="0">Expense</option>
            	</select>
            </td>
        	<td>
        		<div class="input-group date" v-datepicker="ledger.date" >
                    <input type="text" class="form-control" v-model="ledger.date" v-el:date>
                	<span class="input-group-addon">
                    	<i class="glyphicon glyphicon-th"></i>
                    </span>
                </div>
        	</td>
            <td>
            	<input class="form-control"  placeholder="Ledger Name" type="text" v-model="ledger.name" v-el:name>
            </td>
            <td>
            	<select class="form-control" v-model="ledger.category_id">
                	<option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                	<option value="">Other...</option>
              	</select>
            	
               	<input v-if="(! ledger.category_id) || (ledger.category_id == '')" placeholder="Enter ..." class="form-control" type="text" v-model="ledger.category_name">
            </td>
            <td>
            	<select v-if="getAssociationType(ledger) == 'breeder'" class="form-control" v-model="ledger.associated_id">
            		<option value="">Choose...</option>
            		<option v-for="association in breeders" :value="association.id"> @{{ representAssociation(ledger, association) }}</option>
            	</select>
            	<select v-if="getAssociationType(ledger) == 'litter'" class="form-control" v-model="ledger.associated_id">
            		<option value="">Choose...</option>
            		<option v-for="association in litters" :value="association.id"> @{{ representAssociation(ledger, association) }}</option>
            	</select>
            </td>
            <td>
            	<input placeholder="Enter ..." class="form-control" min="0" step="1" type="number" v-model="ledger.amount">
            </td>
			<td>
				<textarea placeholder="Descriptions" rows="2" class="form-control" v-model="ledger.description"></textarea>
			</td>
        </tr>
    </template>	
    
    <div id="wizard__ledger-form-template">
        <form v-if="ledger" id="wizard-ledger-form" class="form-horizontal row-paddings-compensation">
        	<div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Date</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.date"
                           v-el:date
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Name</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.name"
                           v-el:name
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Category</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.category_id"
                           v-el:category_id
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Association</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.associated_id"
                           v-el:associated_id
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Type</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.debit"
                           v-el:debit
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Amount</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.amount"
                           v-el:name
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-6 col-xs-7">
            	<label class="col-sm-4 control-label">Note</label>
                <div class="col-sm-8">
                    <input type="text" v-model="ledger.description"
                           v-el:name
                           placeholder="Enter ..." class="form-control">
                </div>
            </div>
        </form>
    </div>
</template>