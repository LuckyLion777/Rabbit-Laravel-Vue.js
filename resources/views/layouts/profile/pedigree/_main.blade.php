<a @click="edit(generations.g1.id)" role="button" href="javascript: void(0);" v-if="generations.g1">
    <div class="box box-widget widget-user-2">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header @{{ generations.g1.css.color }}">

            <div class="widget-user-image">
                <img style="border: 3px solid; width: 29%; margin-right: 10px; margin-top:-15px; margin-left:-15px"
                     v-if="!generations.g1.image" src="{{asset('')}}media/pedigree/default.jpg" class="img-circle">
                <img style="border: 3px solid; width: 29%; margin-right: 10px; margin-top:-15px; margin-left:-15px"
                     v-if="generations.g1.image.path" v-bind:src="generations.g1.image.path" class="img-circle">
            </div><!-- /.widget-user-image -->

            <h3 class="widget-user-username"><strong>@{{ generations.g1.name }}  </strong><i class="@{{ generations.g1.css.icon }} pull-right"></i></h3>
            <h4 class="widget-user-desc">@{{ generations.g1.tattoo || generations.g1.custom_id }}</h4>

        </div>

        <div class="box-footer">
            <div class="row">
                <div v-if="generations.g1.day_of_birth" class="col-xs-6">
                    <p><strong>DoB:</strong> @{{ generations.g1.day_of_birth }}</p>
                </div>
                
                <div v-if="generations.g1.color" class="col-xs-6">
                	<p><strong>Color:</strong> @{{ generations.g1.color }}</p>
                </div>
                <div v-if="generations.g1.aquired" class="col-xs-6">
                	<p><strong>Acquired:</strong> @{{ generations.g1.aquired }}</p>
                </div>
                <div v-if="generations.g1.breed" class="col-xs-6">
                	<p><strong>Breed:</strong> @{{ generations.g1.breed }}</p>
                </div>
                <div v-if="generations.g1.weight_slug" class="col-xs-6">
                	<p><strong>Weight:</strong> @{{ generations.g1.weight_slug }}</p>
                </div>
                <div v-if="generations.g1.registration_number" class="col-xs-6">
                    <p><strong>Reg #:</strong> @{{ generations.g1.registration_number }}</p>
                </div>
                <div v-if="generations.g1.champion_number" class="col-xs-6">
                    <p><strong>Champ #:</strong> @{{ generations.g1.champion_number }}</p>
                </div>
                <div v-if="generations.g1.legs" class="col-xs-6">
                    <p><strong>Legs:</strong> @{{ generations.g1.legs }}</p>
                </div>
            </div>
            <div class="row" v-if="generations.g1.notes">
            	<hr>
                <div class="col-xs-12"><p>@{{ generations.g1.notes }}</p></div> 
            </div>
         
        </div>
    </div>
</a>
