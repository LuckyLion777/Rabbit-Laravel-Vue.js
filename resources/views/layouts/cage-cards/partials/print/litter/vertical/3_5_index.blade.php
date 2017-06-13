@extends('layouts.cage-cards.partials.print.layout')

@section('content')
    @php $index = 1; @endphp
    @foreach($entitiesDataMap as $entityDataItem)
        <div class="box-wrapper">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        @if($hole == '1')
                            <img src="{{ public_path('img').'/index_hole.png' }}">
                        @else
                            <img src="{{ public_path('img').'/index_nohole.png' }}">
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <h3>
                            @if(isset($entityDataItem['fields'][1]))
                                <strong>{!! $entityDataItem['fields'][1]['value'] !!}</strong>
                                <br>
                            @endif
                            @if(isset($entityDataItem['fields'][2]))
                                <strong>{!! $entityDataItem['fields'][2]['value'] !!}</strong>
                                <br>
                            @endif
                            @if(isset($entityDataItem['fields'][3]))
                                {!! $entityDataItem['fields'][3]['value'] !!}
                            @endif
                        </h3>
                    </div>
                    <div class="col-xs-6">
                        <img class="qr" src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($entityDataItem['url'])) }} ">
                    </div>
                </div>
                <div class="row ">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][4]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][4]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][4]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][5]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][5]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][5]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][6]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][6]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][6]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][7]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][7]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][7]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][8]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][8]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][8]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][9]))
                            <p>
                                <strong> {!! $entityDataItem['fields'][9]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][9]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>

                @if(isset($entityDataItem['fields']['table']) && $entityDataItem['fields']['table']['value'] == 'yes' && isset($entityDataItem['kits']) && count($entityDataItem['kits']))

                    <div class="table-wrapper">
                        <table class="table">
                            <tbody>
                            @foreach($entityDataItem['kits'] as $i => $kit)
                                <tr>
                                    <td>{{ $kit['given_id'] }}</td>
                                    <td>{{ $kit['color'] }}</td>
                                    <td>
                                        @if($kit['sex'] == 'doe')
                                            <img class="venus" src="{{ public_path('img').'/venus.png' }}" alt="">
                                        @endif    
                                        @if($kit['sex'] == 'buck')
                                            <img class="mars" src="{{ public_path('img').'/mars.png' }}" alt="">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @if($index < count($entitiesDataMap))
            <div class="page-break"></div>
        @endif
        @php $index++; @endphp
    @endforeach
@endsection





