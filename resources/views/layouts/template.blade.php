<!DOCTYPE html>
<html>
<head>
    @include('layouts.head')

</head>


<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">
        @include('layouts.topbar')
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        @include('layouts.lsidebar')
    </div>
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-title-box">
                            <h4 class="page-title">{{ $pageTitle }}</h4>
                            <!--<ol class="breadcrumb p-0">
                                <li>
                                    <a href="#">Uplon</a>
                                </li>
                                <li>
                                    <a href="#">Dashboard</a>
                                </li>
                                <li class="active">
                                    Dashboard
                                </li>
                            </ol>-->
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-lg-12">
                        @if ($message = session('success_message'))
                            @include('layouts.alert-success')
                        @elseif ($message = session('fail_message'))
                            @include('layouts.alert-danger')
                        @elseif ($message = session('warning_message'))
                            @include('layouts.alert-warning')
                        @endif
                    </div>
                </div>

                @yield('content')

            </div> <!-- container -->
        </div> <!-- content -->
    </div>
    <!-- End content-page -->


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->


    <!-- Right Sidebar -->
    <div class="side-bar right-bar">
        @include('layouts.rsidebar')
    </div>
    <!-- /Right-bar -->

    <footer class="footer text-right">
        @php echo date('Y') @endphp © Starfleet Enterprises.
    </footer>
</div>
<!-- END wrapper -->

@include('layouts.endbody')

@hasSection('script-source')
    @yield ('script-source')
@endif
<script>
    function setReportDate(value)
    {
        $.ajax({
            type: 'POST',
            url: '{{ route('home.date') }}',
            data: {
                _token: '{{ csrf_token() }}',
                monthyear: value
            },
            success: function (data) {
                if (data.success)
                {
                    toastr["success"](data.message,
                        "Success!", {
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": false,
                            "progressBar": false,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "1000",
                            "hideDuration": "500",
                            "timeOut": "1000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        });
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
</script>

@hasSection('modals')
    @yield('modals')
@endif

</body>
</html>