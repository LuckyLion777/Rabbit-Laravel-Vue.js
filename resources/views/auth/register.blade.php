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
            <h2 class="login-box-msg">Sign up for <strong>FREE</strong></h2>
            <form id="register-form" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}


                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input type="text" placeholder="Full Name" name="name" value="{!! old('name') !!}" class="form-control">
                    <span class="fa fa-user form-control-feedback"></span>
                </div>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" placeholder="Email" name="email" value="{!! old('email') !!}" class="form-control">
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                @if ($errors->has('email_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email_confirmation') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('email_confirmation') ? ' has-error' : '' }}">
                    <input type="email" placeholder="Email confirmation" name="email_confirmation" value="{!! old('email_confirmation') !!}" class="form-control">
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" placeholder="Password" class="form-control">
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <input type="password" name="password_confirmation" placeholder="Password confirmation" class="form-control">
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>


                {{--   ----------------------   --}}


                <div class="row margin">
                	<button class="btn btn-success btn-block btn-lg register" type="submit">Get Started <i class="fa fa-arrow-right"></i></button>
                </div>

            </form>

            <div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="{{ route('auth.forward', ['provider' => 'facebook']) }}" class="btn btn-block btn-social btn-facebook btn-lg register-social"><i class="fa fa-facebook"></i> Sign up using
                    Facebook</a>
                <a href="{{ route('auth.forward', ['provider' => 'google']) }}" class="btn btn-block btn-social btn-google btn-lg register-social"><i class="fa fa-google-plus"></i> Sign up using
                    Google+</a>
            </div>
            <!-- /.social-auth-links -->

        </div><!-- /.login-box-body -->

        <div class="row margin text-center">
        	<br>
            <a href="{{ url('/login') }}" class="btn btn-outline btn-block btn-lg">Already a Member? Sign in</a>
            <br><br>
            <a href="http://barntrax.com" class="btn btn-outline">What is Hutch?</a>
        </div>
    </div><!-- /.login-box -->

    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=284639101919664&ev=PageView&noscript=1"/></noscript>
@endsection

@section('scripts')
    <script type="text/javascript">
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '284639101919664');
        fbq('track', 'PageView');
        fbq('track', 'ViewContent', { content_name: 'Register Page'  } );

        $(".register").on('click', function(event) { // when someone clicks these links
            event.preventDefault();
            ga('send', 'event', 'Register', 'Click', 'Register Button');
            fbq('track', 'CompleteRegistration', {content_name: 'Register', currency: 'USD', value: 3});
            setTimeout(function(){
                $("#register-form").submit();
            }, 500);

        });
		
		$(".register-social").on('click', function(event) { // when someone clicks these links
            ga('send', 'event', 'Register', 'Social', 'Social Register Button');
            fbq('track', 'CompleteRegistration', {content_name: 'Register', currency: 'USD', value: 3});
        });


    </script>
@endsection

