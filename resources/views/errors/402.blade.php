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
        <div class="text-error">4<span class="ion-sad"></span>2</div>
        <h3 class="text-uppercase text-white font-600">Subscription Past Due</h3>
        <p class="text-white m-t-30">
            Your subscription is past due. Please make immediate payment to continue using services.
        </p>
        <br>
        <a class="btn btn-pink waves-effect waves-light" href="{{ route('payments.create') }}"> Make Payment</a>

    </div>


</div>
<!-- end wrapper page -->


@include('layouts.endbody')

</body>
</html>