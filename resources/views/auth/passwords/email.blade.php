@extends('layouts.default')

<!-- Main Content -->
@section('content')

    <body class="hold-transition login-page bg-green">
    <div class="login-box">
        <div class="login-logo">
            <img alt="HUTCH" src="{{ url('/') }}/img/logo.png"><br>
            <a href="{{ url('/') }}"><b>HUTCH</b></a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <form role="form" method="POST" id="password_reset" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}

                @if (session('status'))
                    <div class="alert alert-info">
                        {{ session('status') }}
                    </div>

                @else

                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                    <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input type="email" placeholder="Email" name="email" class="form-control input-lg">
                        <span class="fa fa-envelope form-control-feedback"></span>
                    </div>

                    <div class="row margin">
                        <button class="btn btn-success btn-block btn-lg" type="submit">Reset My Password</button>
                    </div>

                @endif
            </form>

            <!-- /.social-auth-links -->

        </div><!-- /.login-box-body -->
        
        <div class="row margin text-center">
        	<br>
            <a href="{{ url('/login') }}" class="btn btn-outline btn-lg">Sign in</a> <a href="{{ url('/register') }}" class="btn btn-outline btn-lg">Register</a>
            
        </div>
    </div><!-- /.login-box -->

@endsection
