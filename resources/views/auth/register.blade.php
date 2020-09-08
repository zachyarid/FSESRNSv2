<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App Favicon -->
    <link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}">

    <!-- App title -->
    <title>SRNS - Register</title>

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
                        <h6 class="text-muted text-uppercase m-b-0 m-t-0">Sign Up</h6>
                    </div>
                </div>
                <form class="m-t-20" action="{{ route('register') }}" method="post">
                    @if (isset($errors))
                        <div class="col-xs-12 text-center m-b-10 row has-errors">
                            <span class="text-danger">
                                <strong>{{ $errors->first() }}</strong>
                            </span>
                        </div>
                    @endif
                    @csrf
                    <p class="text-muted text-xs-center" style="color:red;"></p>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" value="{{ old('fname') }}" name="fname" placeholder="First Name" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" value="{{ old('lname') }}" name="lname" placeholder="Last Name">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" value="{{ old('username') }}" name="username"  placeholder="FSE Username">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" name="personal_key" value="{{ old('personal_key') }}" placeholder="Personal Access Key">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" name="password" placeholder="Password">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" name="password_confirmation" placeholder="Confirm Password">
                        </div>
                    </div>

                    <div class="form-group row text-center m-t-10">
                        <div class="col-xs-12">
                            <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Join Now</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- end card-box-->

    <div class="m-t-20">
        <div class="text-xs-center">
            <p class="text-white">Already have account? <a href="{{ route('login') }}" class="text-white m-l-5"><b>Sign In</b> </a></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->

@include('layouts.endbody')

</body>
</html>