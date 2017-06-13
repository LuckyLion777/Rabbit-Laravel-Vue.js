@extends('layouts.cage-cards.partials.print.layout')

@section('content')
    @php $index = 1; @endphp
    @foreach($entitiesDataMap as $entityDataItem)
        <div class="box-wrapper">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-4"><img src="{{ public_path('img').'/pedigree_logo.png' }}" style="max-width:100%"></div>
                    <div class="col-xs-8">
                        <h3 style="padding-left: 10px;">
                            @if(isset($entityDataItem['fields'][1]))
                                <strong>{!! $entityDataItem['fields'][1]['value'] !!}</strong>
                            @endif
                            @if(isset($entityDataItem['fields'][2]))
                                <strong>{!! $entityDataItem['fields'][2]['value'] !!}</strong>
                            @endif
                        </h3>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-xs-3"><img style="qr" src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($entityDataItem['url'])) }} "></div>
                    <div class="col-xs-3">
                        @if(isset($entityDataItem['fields'][3]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][3]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][3]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][4]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][4]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][4]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][5]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][5]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][5]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][6]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][6]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][6]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][7]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][7]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][7]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-3">
                        @if(isset($entityDataItem['fields'][8]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][8]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][8]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][9]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][9]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][9]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][10]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][10]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][10]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][11]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][11]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][11]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][12]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][12]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][12]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-3">
                        @if(isset($entityDataItem['fields'][13]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][13]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][13]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][14]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][14]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][14]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][15]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][15]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][15]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][16]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][16]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][16]['value'] !!}
                            </p>
                        @endif
                        @if(isset($entityDataItem['fields'][17]))
                            <p class="index">
                                <strong> {!! $entityDataItem['fields'][17]['name'] !!}</strong>
                                {!! $entityDataItem['fields'][17]['value'] !!}
                            </p>
                        @endif
                    </div>


                </div>

                @if(isset($entityDataItem['fields']['table']) && $entityDataItem['fields']['table']['value'] == 'yes')
                    <!--<div class="table-wrapper"> -->
                        <table class="table min-height">

                            <tr>
                                <td>Buck</td>
                                <td>Bred</td>
                                <td>Kindle</td>
                                <td># Born</td>
                                <td># Died</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            
                        </table>
                    <!--</div>-->
                @endif
            </div><!-- /.box-body -->
        </div>
        @if($index < count($entitiesDataMap))
            <div class="page-break"></div>
        @endif
        @php $index++; @endphp
    @endforeach
@endsection
