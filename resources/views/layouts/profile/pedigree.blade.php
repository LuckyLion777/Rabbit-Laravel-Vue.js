<template id="pedigree-template">
    <div class="row" v-if="loadingPedigree">
        <div class="col-xs-1 col-xs-offset-5">
            <img src="/img/ajax-loader.gif" alt="Loading..." class="loader">
        </div>
    </div>

    <div v-if="!loadingPedigree" class="box-header">
        <!-- tools box -->
        <a class="btn btn-default" href="#" @click.prevent="showCopy"><i class="fa fa-copy"></i>
            <strong>Copy Data</strong>
        </a>

        <a class="btn btn-primary" href="{{url("admin/breeders")}}/@{{id}}/pdf"><i class="fa fa-file-pdf-o"></i>
            <strong>Generate PDF</strong></a>

        <a class="btn btn-success" target="_blank" href="{{ strtr(route('web.pedigree', ['id' => '%id']), ['%id' => '{' . '{token}' . '}']) }}">
            <i class="fa fa-external-link"></i> <strong>Public link</strong>
        </a>
        <!-- /. tools -->
    </div><!-- /.box-header -->
    <div v-if="!loadingPedigree" class="box-body">
        <div class="row row-horizon pedigree">

            <!-- START GENERATION 1 -->
            <!-- Hide GENERATION 1
            <div class="col-xs-12 col-sm-6 hidden-md hidden-lg">
                <div class="whole"></div>
                <div class="whole"></div>
                <div class="box box-widget widget-user-2">
                    
                    <div class="widget-user-header @{{ generations.g1.css.color }}">
                        <div class="widget-user-image">
                            <img style="border: 3px solid; width: 35%; margin-right: 10px; margin-top:-10px; margin-left:-10px"
                                 v-if="!generations.g1.image" src="{{asset('')}}media/pedigree/default.jpg"
                                 class="img-circle">
                            <img style="border: 3px solid; width: 35%; margin-right: 10px; margin-top:-10px; margin-left:-10px"
                                 v-if="generations.g1.image.path" v-bind:src="generations.g1.image.path"
                                 class="img-circle">


                        </div><!-- /.widget-user-image --
                        <h3 class="widget-user-username"><strong>@{{ generations.g1.name }}  </strong><i
                                    class="@{{ generations.g1.css.icon }} pull-right"></i></h3>
                        <h4 class="widget-user-desc">@{{ generations.g1.tattoo }}</h4>

                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-6 border-right "><p class="text-center">
                                    <strong>DoB:</strong> @{{ generations.g1.date_of_birth }} </p></div>

                            <div class="col-xs-6"><p class="text-center">
                                    <strong>Breed:</strong> @{{ generations.g1.breed }}</p></div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-6 border-right"><p class="text-center">
                                    <strong>Acquired:</strong> @{{ generations.g1.aquired }}</p></div>

                            <div class="col-xs-6"><p class="text-center">
                                    <strong>Color:</strong> @{{ generations.g1.color }}</p></div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-6 border-right ">@{{ generations.g1.notes }}</div>
                            <div class="col-xs-6 "><p class="text-center">
                                    <strong>Weight:</strong> @{{ generations.g1.weight_slug }}</p></div>
                        </div>

                    </div>
                </div>


            </div>
            -->
            <!-- END GENERATION 1 -->
			
			{{--*/$g = Auth::user()->pedigree_number_generations/*--}}
			
			@if($g == 2)
				{{--*/$b = 2/*--}}
			@elseif($g == 1)
                {{--*/$b = 1/*--}}
            @else
				{{--*/$b = pow(2, $g-1)/*--}}
			@endif
			{{--*/$l = 1/*--}}
			@for($i=1; $i<=$g; $i++)
				<div class="col-xs-12 col-sm-6 col-md-10 col-lg-4" v-if="generations.g{{$i}}">
				@for($j=1;$j<=$l;$j++)
					
						<!-- FIRST PARENT -->
						@if($b>4)
							@for($k=1; $k<=($b/4-1); $k++)
								<div class="whole"></div>
							@endfor
						@endif
						
						@if($b>2)
							<div class="half"></div>
						@endif
                        @if($i==1)
                            <div class="half"></div>
                            @if($b>4)
                                @for($k=1; $k<=($b/4-1); $k++)
                                    <div class="whole"></div>
                                @endfor
                            @endif
                            
                            @if($b>2)
                                <div class="half"></div>
                            @endif
                            @include('layouts.profile.pedigree._other', ['g' => 'g1'])
                        @else
    						@include('layouts.profile.pedigree._other', ['g' => 'g'.$i.'.f'.$j])
                        @endif

						<!-- SECOND PARENT -->
						
						@if($b>2)
							<div class="half"></div>
						@endif
						
						@if($b>4)
							@for($k=1; $k<=($b/4-1); $k++)
								<div class="whole"></div>
							@endfor
						@endif
						@if($b>4)
							@for($k=1; $k<=($b/4-1); $k++)
								<div class="whole"></div>
							@endfor
						@endif
						@if($b>2)
							<div class="half"></div>
                        @endif
						
						@include('layouts.profile.pedigree._other', ['g' => 'g'.$i.'.m'.$j])
                        
						@if($b>2)
							<div class="half"></div>
                        @endif
                        
						@if($b>4)
							@for($k=1; $k<=($b/4-1); $k++)
								<div class="whole"></div>
							@endfor
                        @endif

				@endfor
				</div>
                @if($i!=1)
				{{--*/$b /= 2/*--}} 
				{{--*/$l *= 2/*--}} 
                @endif
			@endfor
			


           


        </div>
        <!--The calendar -->
        <!-- tools box -->
        <a class="btn btn-primary" href="{{url("admin/breeders")}}/@{{id}}/pdf"><i class="fa fa-file-pdf-o"></i>
            <strong>Generate PDF</strong></a>
        <a class="btn btn-success" target="_blank" href="{{ strtr(route('web.pedigree', ['id' => '%id']), ['%id' => '{' . '{token}' . '}']) }}">
            <i class="fa fa-external-link"></i> <strong>Public link</strong>
        </a>
        <!-- /. tools -->

    </div><!-- /.box-body -->


    @include('layouts.breeders.partials.pedigree', ['type' => 'breeder', 'id' => 'generations.g1.id', 'name' => 'generations.g1.name' ])


</template>
