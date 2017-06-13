@extends('layouts.default')

@section('content')
    <body class="sidebar-mini skin-green">
    <div id="vue-app" class="wrapper">
        <div class="content-wrapper" style="margin-left: 0;">
            <!-- Main content -->
            <div id="vue-content">

                @include('layouts.partials.error')

                <div id="wizard__settings">
                <form method="POST" action="{{ route('auth.email', ['social' => $social]) }}">
                    {!! csrf_field() !!}

                    <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"><br><br>
                        <div class="box box-solid bg-green-gradient">

                            <!-- /.box-header -->
                            <div class="box-body"><h2 class="text-center"><img src="{{ asset('img/logo-tiny.png') }}"><br>Welcome to Hutch!</h2>

                                <h4 class="text-center">Let's start by setting up your account</h4>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                            <div class="box box-solid box-primary">
                                <div class="box-header text-center"><h3 class="box-title"><i class="fa fa-envelope"></i> Email Address</h3></div>
                                <div class="box-body">
                                    <div class="row text-center">
                                        <p>Please enter an email address to create your account.</p>
                                        <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
                                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                                <label for="" class="">Email Address</label>
                                                <input type="email" class="form-control input-lg" name="email">
                                                @if ($errors->has('email'))
                                                    <p class="error">{{ $errors->get('email')[0] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <span> Save Email <i class="fa fa-arrow-circle-right"></i></span>
                        </button>
                        <br><br><br><br>
                    </div>

                </form>
                </div>
            </div>
        </div>
        @include('layouts.partials.footer')
    </div>
@endsection

