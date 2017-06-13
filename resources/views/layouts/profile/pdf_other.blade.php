{{-- @if($breeder->name) --}}
<table border="0" cellspacing="0" cellpadding="0" class="border">
    <tr>
    	@if($breeder->image && isset($breeder->image['name']) && $breeder->image['name'])
        <td class="bg-white" width="1">
            <img src="{{$breeder->image['path']}}" class="img-circle" width="50">
        </td>
        <td class="no-boder-left bg-white" valign="top">
        @else
        <td class="bg-white" valign="top">
        @endif
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="name"><strong>{{$breeder->name}}</strong>@if($breeder->custom_id): {{$breeder->custom_id}} @endif </td>
                    <td width="1" valign="top" align="right"><img src="{{$directory .  'img/' . $breeder->css['img']}}" width="13"></td>
                </tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50%">
                    	@if($breeder->day_of_birth)DoB: {{$breeder->day_of_birth}} <br />@endif
                        @if($breeder->aquired)ACQ: {{$breeder->aquired}}<br /> @endif
                        @if($breeder->registration_number)Reg#: {{$breeder->registration_number}}<br /> @endif
                        @if($breeder->champion_number)GC#: {{$breeder->champion_number}}<br /> @endif
                        &nbsp;
                    </td>
                    <td width="50%" align="right">
                     	@if($breeder->color)Color: {{$breeder->color}}<br /> @endif
                        @if($breeder->breed)Breed: {{$breeder->breed}}<br /> @endif
                        @if($breeder->weight)Weight: {{$breeder->weight_slug}}<br /> @endif
                        @if($breeder->legs)Legs: {{$breeder->legs}}<br /> @endif
                        &nbsp;
                     </td>
                </tr>
                @if($breeder->notes)
                <tr>
                    <td colspan="2">{{$breeder->notes}}</td>
                </tr>
                @endif

            </table>
        </td>
    </tr>
</table>
{{-- @endif --}}
