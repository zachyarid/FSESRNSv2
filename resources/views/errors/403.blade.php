<!DOCTYPE html>
<html>
<head>
    @include('layouts.head')
</head>


<body>

<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">

    <div class="ex-page-content text-xs-center">
        <div class="text-error">4<span class="ion-sad"></span>3</div>
        <h3 class="text-uppercase text-white font-600">Unauthorized Access</h3>
        <p class="text-white m-t-30">
            This incident has been logged. If you believe that this is an error, please contact an administrator.
        </p>
        <br>
        <a class="btn btn-pink waves-effect waves-light" href="{{ route('home') }}"> Return Home</a>

    </div>


</div>
<!-- end wrapper page -->


@include('layouts.endbody')

</body>
</html>