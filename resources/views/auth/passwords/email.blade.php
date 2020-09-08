
<!DOCTYPE html>
<html>
<head>
    @include('layouts.head')
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
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row">
                    <div class="col-xs-12 text-xs-center">
                        <h6 class="text-muted text-uppercase m-b-0 m-t-0">Reset Password</h6>
                        <p class="text-muted m-b-0 font-13 m-t-20">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                    </div>
                </div>
                <form class="m-t-30" action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="form-group row{{ $errors->has('email') ? ' has-errors' : '' }}">
                        <div class="col-xs-12">
                            <input class="form-control" type="email" value="{{ old('email') }}" name="email" placeholder="Enter email">
                        </div>
                    </div>

                    @if ($errors->has('email'))
                        <div class="col-xs-12 text-center m-b-10 row{{ $errors->has('email') ? ' has-errors' : '' }}">
                            <span class="text-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        </div>
                    @endif

                    <div class="form-group row text-center m-t-20 m-b-0">
                        <div class="col-xs-12">
                            <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Send Email</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- end card-box-->

    <div class="m-t-20">
        <div class="text-xs-center">
            <p class="text-white">Return to<a href="{{ route('login') }}" class="text-white m-l-5"><b>Sign In</b></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->

@include('layouts.endbody')

</body>
</html>