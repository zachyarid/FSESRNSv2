<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App title -->
    <title>SRNS - Login</title>

    <!-- App CSS -->
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="{{ url('assets/js/modernizr.min.js') }}"></script>

</head>


<body>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="account-bg">
        <div class="card-box m-b-0">
            <div class="text-xs-center m-t-20">
                <a href="{{ route('home') }}" class="logo">
                    <img width="25px" height="25px"  src="{{ url('assets/images/favicon.ico') }}" />
                    <span>Starfleet</span>
                </a>
            </div>
            <div class="m-t-10 p-20">
                <div class="row">
                    <div class="col-xs-12 text-xs-center">
                        <h6 class="text-muted text-uppercase m-b-0 m-t-0">Sign In</h6>
                    </div>
                </div>

                <form class="m-t-20" action="{{ route('login') }}" method="post">
                    @csrf

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" name="email" placeholder="Username or Email" value="{{ old('email') }}" autofocus />
                        </div>
                    </div>

                    @if ($errors->has('email'))
                        <div class="col-xs-12 text-center m-b-10 row{{ $errors->has('email') ? ' has-errors' : '' }}">
                            <span class="text-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        </div>
                    @endif

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password" placeholder="Password" value="{{ old('password') }}" />
                        </div>
                    </div>

                    @if ($errors->has('password'))
                        <div class="col-xs-12 text-center m-b-10 row{{ $errors->has('password') ? ' has-errors' : '' }}">
                            <span class="text-danger">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        </div>
                    @endif

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <div class="checkbox checkbox-custom">
                                <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="checkbox-signup">
                                    Remember me
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center row m-t-10">
                        <div class="col-xs-12">
                            <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </div>

                    <div class="form-group row m-t-30 m-b-0">
                        <div class="col-sm-12">
                            <a href="{{ route('password.request') }}" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
    <!-- end card-box-->

    <div class="m-t-20">
        <div class="text-xs-center">
            <p class="text-white">Don't have an account? <a href="{{ route('register') }}" class="text-white m-l-5"><b>Sign Up</b></a></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->

@include('layouts.endbody')

</body>
</html>
