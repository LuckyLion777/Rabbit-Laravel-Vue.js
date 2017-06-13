@extends('layouts.public')

@section('content')

<section class="content">
<!-- Your Page Content Here -->
  <div class="row">
    <section class="col-lg-12">
      <div class="box box-solid box-success ">
        <div class="box-header"><img src="{{ asset("") . 'img/logo-tiny.png'}}" alt="HUTCH">
          <h1 class="box-title"><strong> HUTCH</strong> Pedigree</h1>
          <div class="pull-right"><a target="_blank" href="{{ route('web.invite', [ 'inviter' => $owner->getSlug() ]) }}"><strong>Get Hutch &gt;&gt;</strong></a></div>
        </div><!-- /.box-header -->
        <div class="box-body">
        	<div class="row row-horizon pedigree">
              <div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">
                <div style="padding-left: 10px;">
                    @if($pedigree['g1']->user->pedigree_logo['path'])
                        <img src="{{ $pedigree['g1']->user->pedigree_logo['path']}}" style="max-width:100%"><br /><br />
                    @endif
                    {!! nl2br($pedigree['g1']->user->pedigree_rabbitry_information) !!}
                 </div>
                 <div class="half"></div>
                 <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header {{$pedigree['g1']['css']['color']}}">
                    <div class="widget-user-image">
                      <img style="border: 3px solid; width: 21%; margin-right: 20px; margin-top:-10px" src="{{$pedigree['g1']['image']['path']}}" class="img-circle">
                    </div><!-- /.widget-user-image -->
                    <h3 class="widget-user-username">
                    <small style="color: white; font-size: 10px; font-weight:bold;">{{$pedigree['g1']['prefix']}}</small><br>
                    <strong>{{$pedigree['g1']['name']}} </strong><i class="{{$pedigree['g1']['css']['icon']}} pull-right"></i></h3>
                    <h4 class="widget-user-desc">{{$pedigree['g1']['tattoo'] ?: $pedigree['g1']['custom_id']}}</h4>
				  </div>
                  <div class="box-footer">
  					<div class="row">
                      @if($pedigree['g1']['day_of_birth']||$pedigree['g1']['date_of_birth'])
                        <div class="col-xs-6"><p><strong>DoB:</strong> {{$pedigree['g1']['day_of_birth'] ?: $pedigree['g1']['date_of_birth']}} </p></div>
                      @endif

                      @if($pedigree['g1']['color'])
                        <div class="col-xs-6"><p><strong>Color:</strong> {{$pedigree['g1']['color']}}</p></div>
                      @endif

                      @if($pedigree['g1']['aquired'])
                        <div class="col-xs-6"><p><strong>ACQ:</strong> {{$pedigree['g1']['aquired']}}</p></div>
                      @endif

                      @if($pedigree['g1']['breed'])
                        <div class="col-xs-6"><p><strong>Breed:</strong> {{$pedigree['g1']['breed']}}</p></div>
                      @endif

                      @if($pedigree['g1']['weight'])
                        <div class="col-xs-6"><p><strong>Weight:</strong> {{$pedigree['g1']['weight_slug']}}</p></div>
                      @endif

                      @if($pedigree['g1']['registration_number'])
                        <div class="col-xs-6"><p><strong>Reg#:</strong> {{$pedigree['g1']['registration_number']}}</p></div>
                      @endif

                      @if($pedigree['g1']['champion_number'])
                          <div class="col-xs-6"><p><strong>GC#:</strong> {{$pedigree['g1']['champion_number']}}</p></div>
                      @endif

                      @if($pedigree['g1']['legs'])
                      <div class="col-xs-6"><p><strong>Legs:</strong> {{$pedigree['g1']['legs']}}</p></div>
                      @endif
                      
					  @if($pedigree['g1']['notes'])
                      <div class="col-xs-12"><p>{{$pedigree['g1']['notes']}}</p></div>
                      @endif
                    </div>
                  </div>
                </div><!-- box -->
                <br><br><br>
                <a  target="_blank" href="{{ route('web.invite', [ 'inviter' => $owner->getSlug() ]) }}"><img src="{{ asset("") . 'img/pedigree_logo.png'}}" border="0"></a>
              </div>

			  
			  
			  
			  
			{{--*/$g = Auth::user()->pedigree_number_generations/*--}}
			
			@if($g == 2)
				{{--*/$b = 2/*--}}
			@else
				{{--*/$b = pow(2, $g-1)/*--}}
			@endif
			{{--*/$l = 1/*--}}
			@for($i=2; $i<=$g; $i++)
				<div class="col-xs-12 col-sm-6 col-lg-3">
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
						@include('layouts.profile.public_pedigree',['r'=>$pedigree['g'.$i]['f'.$j]])	

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
						
                        @include('layouts.profile.public_pedigree',['r'=>$pedigree['g'.$i]['m'.$j]])	
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
				{{--*/$b /= 2/*--}} 
				{{--*/$l *= 2/*--}} 
			@endfor  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  

        

          </div><!--The calendar -->
        </div><!-- /.box-body -->
      </div>
    </section>
  </div>
</section>

{{--
@include('layouts.profile.pdf',['pedigree'=>$pedigree,'directory'=> asset("/"),'isPublic'=>true])
--}}
@endsection
