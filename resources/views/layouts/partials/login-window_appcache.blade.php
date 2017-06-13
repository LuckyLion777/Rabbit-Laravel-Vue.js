<div class="login-window hold-transition login-page bg-green">
    <div class="login-box hide">
        <div class="login-logo">
        	<img alt="HUTCH" src="img/logo.png"><br>
            <a href="{{ url('/') }}"><b>HUTCH</b></a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <form role="form" method="POST" id="login-form" action="{{ url('/login') }}">


                <div class="alert alert-warning hide" id="ajax-errors">
                </div>

                {!! csrf_field() !!}

                @if ($errors->has('email'))
                    <span class="help-block error">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" placeholder="Email" name="email" id="email" class="form-control">
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                @if ($errors->has('password'))
                    <span class="help-block error">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" id="passwd" placeholder="Password" class="form-control">
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="rememberme" value="1">
                                Remember Me
                            </label>
                        </div>
                    </div><!-- /.col -->
                </div>
                <div class="row margin">
                	<button class="btn btn-success btn-block btn-lg" type="submit">SIGN IN</button>
                </div>
                <div class="row text-center">
                	<a class="small" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                 </div>
            </form>

            <!-- /.social-auth-links -->


        </div><!-- /.login-box-body -->
        
        <div class="row margin">
        	<br>
            <a href="{{ url('/register') }}" class="btn btn-outline btn-block btn-lg">New to Hutch? Register Here</a>
        </div>

        <div class="alert alert-warning hide-online">
            <b>You currently is offline. You can login only if you previously have logged in on this computer with
                specified account.</b>
        </div>
    </div><!-- /.login-box -->
</div>