{{-- @if($r['name']) --}}
<div>
                <div class="info-box {{$r['css']['color']}} {{$r['sex']}}">
                	@if($r['image']['path'] != "https://htch.us/media/pedigree/default.jpg")
                    <span class="info-box-icon">
                        <img style="max-width: 80%; margin:10px auto; border: 3px solid" src="{{$r['image']['path']}}" class="img-responsive img-circle">
                    </span>
                     @endif
                    <div class="info-box-content @if($r['image']['path'] == "https://htch.us/media/pedigree/default.jpg") no-image @endif">
                        <small style="font-size: 10px; font-weight:bold;">{{ $r['prefix'] }}</small><span class="info-box-number">{{ $r['name'] }}@if($r['custom_id']): {{ $r['custom_id'] }}@endif <i class="{{$r['css']['icon']}} pull-right"></i></span>
                        <span class="info-box-text">
                        	<span class="pull-left" style="@if($r['image']['path'] == "https://htch.us/media/pedigree/default.jpg")max-width:30% @elsemax-width:40% @endif">
                              @if($r['day_of_birth']) DoB: {{ $r['day_of_birth'] }}<br/> @endif
                              @if($r['aquired']) ACQ: {{ $r['aquired'] }}<br/> @endif
                              @if($r['registration_number']) Reg#: {{ $r['registration_number'] }}<br/> @endif
                              @if($r['champion_number']) GC#: {{ $r['champion_number'] }}<br/> @endif
                            </span>  
                            <span class="pull-right" style="@if($r['image']['path'] == "https://htch.us/media/pedigree/default.jpg")max-width:70% @elsemax-width:60% @endif">
                            	@if($r['color'])Color: {{ $r['color'] }}<br/> @endif
                                @if($r['breed'])Breed: {{ $r['breed'] }}<br/> @endif
                                @if($r['weight'])Weight: {{ $r['weight_slug'] }}<br/> @endif
                                @if($r['legs']) Legs: {{ $r['legs'] }}<br/> @endif
                            </span>
                            <div class="clearfix"></div>
                            @if($r['notes'])
                            <span class="notes">{{$r['notes']}}</span>
                            @endif
                        </span>
                    </div>
                </div>
</div>
{{--
@else
<div style="height: 90px">&nbsp;</div>
@endif
--}}
