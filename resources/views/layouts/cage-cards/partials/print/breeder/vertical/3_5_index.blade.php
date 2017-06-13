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
                                <small>{!! $entityDataItem['fields'][1]['value'] !!}</small>
                            @endif
                            @if(isset($entityDataItem['fields'][2]))
                                <br>
                                <strong>{!! $entityDataItem['fields'][2]['value'] !!}</strong>
                            @endif
                            @if(isset($entityDataItem['fields'][3]))
                                <br>{!! $entityDataItem['fields'][3]['value'] !!}
                            @endif
                        </h3>
                    </div>
                    <div class="col-xs-6">
                        <img class="qr" src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($entityDataItem['url'])) }} ">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][4]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][4]['name'] !!} </strong>{!! $entityDataItem['fields'][4]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][5]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][5]['name'] !!} </strong>{!! $entityDataItem['fields'][5]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][6]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][6]['name'] !!} </strong>{!! $entityDataItem['fields'][6]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][7]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][7]['name'] !!} </strong>{!! $entityDataItem['fields'][7]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][8]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][8]['name'] !!} </strong>{!! $entityDataItem['fields'][8]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][9]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][9]['name'] !!} </strong>{!! $entityDataItem['fields'][9]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][10]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][10]['name'] !!} </strong>{!! $entityDataItem['fields'][10]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][11]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][11]['name'] !!} </strong>{!! $entityDataItem['fields'][11]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][12]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][12]['name'] !!} </strong>{!! $entityDataItem['fields'][12]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][13]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][13]['name'] !!} </strong>{!! $entityDataItem['fields'][13]['value'] !!}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][14]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][14]['name'] !!} </strong>{!! $entityDataItem['fields'][14]['value'] !!}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(isset($entityDataItem['fields'][15]))
                            <p class="index">
                                <strong>{!! $entityDataItem['fields'][15]['name'] !!} </strong>{!! $entityDataItem['fields'][15]['value'] !!}
                            </p>
                        @endif
                    </div><!-- /.col-xs-6 -->
                </div><!-- /.row -->
            </div><!-- /.box-body -->
        </div><!-- /.box-wrapper -->
        @if($index < count($entitiesDataMap))
            <div class="page-break"></div>
        @endif
        @php $index++; @endphp
    @endforeach
@endsection


