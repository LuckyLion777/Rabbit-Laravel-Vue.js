<table border="0" cellspacing="0" cellpadding="0" class="border">
    <thead>
        <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" style="padding: 5px 10px;">
                <tr>
                    @if($breeder->image && isset($breeder->image['name']) && $breeder->image['name'])
                        <td width="1"><img src="{{$breeder->image['path']}}" class="img-circle" width="80"></td>
                        <td class="main-top">
                    @else
                    <td class="main-top">
                    @endif
                    <strong>{{$breeder->name}}</strong>
                        <br>
                        <small>{{ @$breeder->tattoo ?: @$breeder->custom_id }}</small>
                    </td>
                    <td width="1" valign="top"><img src="{{$directory .  'img/' . $breeder->css['img']}}" width="15"></td>
                </tr>
            </table>
        </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="3" class="table-bordered">
                    <tr>
                        <td width="50%" class="cell-padding">                        
                        	@if($breeder->date_of_birth || $breeder->day_of_birth) <strong>DoB</strong>: {{ @$breeder->date_of_birth ?: @$breeder->day_of_birth }} <br />@endif
                         	@if($breeder->aquired) <strong>ACQ</strong>: {{$breeder->aquired}}<br /> @endif
                        	@if($breeder->registration_number) <strong>Reg#</strong>: {{$breeder->registration_number}}<br /> @endif
                        	@if($breeder->champion_number) <strong>GC#</strong>: {{$breeder->champion_number}}<br /> @endif 
                         </td>
                        <td width="50%" class="cell-padding">
                        	@if($breeder->color) <strong>Color</strong>: {{$breeder->color}}<br /> @endif
                            @if($breeder->breed) <strong>Breed</strong>: {{$breeder->breed}}<br /> @endif
                            @if($breeder->weight_slug) <strong>Weight</strong>: {{$breeder->weight_slug}}<br /> @endif
                            @if($breeder->legs) <strong>Legs</strong>: {{$breeder->legs}}<br /> @endif
                        </td>
                    </tr>
                    
                    @if($breeder->notes)
                    <tr>
                        <td class="cell-padding" colspan="2">{{$breeder->notes}}</td>
                    </tr>
                    @endif
                    
                </table>
            </td>
        </tr>
    </tbody>
</table>
