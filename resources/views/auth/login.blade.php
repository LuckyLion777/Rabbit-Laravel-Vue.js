@extends('layouts.default')

@section('content')
    <body class="hold-transition login-page bg-green">
    @include('modals.ie')
    <div class="login-box">
        <div class="login-logo">
        	<img alt="HUTCH" src="img/logo.png"><br>
            <a href="{{ url('/') }}"><b>HUTCH</b></a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <form role="form" method="POST" id="login-form">

                @if( session('csrf_error'))
                <div class="alert alert-warning" id="ajax-errors">
                    {{ session('csrf_error') }}
                </div>
                @endif
                {!! csrf_field() !!}

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" placeholder="Email" name="email" id="email" class="form-control input-lg">
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" id="passwd" placeholder="Password" class="form-control input-lg">
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <label>
                            <input type="checkbox" name="rememberme" value="1" checked> Remember Me
                        </label>
                    </div><!-- /.col -->
                </div>
                <div class="row margin">
                	<button class="btn btn-success btn-block btn-lg" type="submit">SIGN IN</button>
                </div>
                <div class="row text-center">
                	<a class="small" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                 </div>
            </form>

            <div class="row text-center"><hr>
                <div class="col-xs-6">
                	<a href="{{ route('auth.forward', ['provider' => 'facebook']) }}" class="btn btn-block btn-social btn-facebook"><i class="fa fa-facebook"></i> <span class="hidden-xs">Use </span>Facebook</a>
                </div>
                <div class="col-xs-6">
                	<a href="{{ route('auth.forward', ['provider' => 'google']) }}" class="btn btn-block btn-social btn-google"><i class="fa fa-google-plus"></i> <span class="hidden-xs">Use </span>Google+</a>
                </div>
            </div>
            <!-- /.social-auth-links -->

        </div><!-- /.login-box-body -->

        <div class="row margin">
        	<br>
            <a href="{{ url('/register') }}" class="btn btn-outline btn-block btn-lg">New to Hutch? Register Here</a>
        </div>

        <div class="alert alert-warning hide-online">

        </div>
    </div><!-- /.login-box -->

@endsection


@section('scripts')
    <script>$(App.Login);</script>
@endsection
