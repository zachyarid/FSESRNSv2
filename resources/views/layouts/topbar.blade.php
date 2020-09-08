<!-- LOGO -->
<div class="topbar-left">
    <a href="" class="logo">
        <img width="20px" height="20px" src="{{ url('assets/images/favicon.ico') }}" />
        <span>Starfleet</span></a>
</div>


<nav class="navbar navbar-custom">
    <ul class="nav navbar-nav">
        <li class="nav-item">
            <button class="button-menu-mobile open-left waves-light waves-effect">
                <i class="zmdi zmdi-menu"></i>
            </button>
        </li>
        <li class="nav-item">
            <form role="search" class="app-search">
                <input type="month" name="month" class="form-control" value="{{ session()->get('monthyear') }}" onchange="setReportDate(this.value)" />
            </form>
        </li>
        <!--<li class="nav-item hidden-mobile">
            <form role="search" class="app-search">
                <input type="text" placeholder="Search..." class="form-control">
                <a href=""><i class="fa fa-search"></i></a>
            </form>
        </li>-->
    </ul>

    <ul class="nav navbar-nav pull-right">
        <li class="nav-item dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false">
                <i class="zmdi zmdi-notifications-none noti-icon"></i>
                <span class="noti-icon-badge"></span><!-- has notification -->
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-lg" aria-labelledby="Preview">
                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h5><small><span class="label label-danger pull-xs-right">0</span>Notification</small></h5>
                </div>

                <!-- All-->
                <a href="javascript:void(0);" class="dropdown-item notify-item notify-all">
                    View All
                </a>

            </div>
        </li>

        <li class="nav-item dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
               aria-haspopup="false" aria-expanded="false">
                <img src="{{ url('assets/images/users/avatar-1.jpg') }}" alt="user" class="img-circle">
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-arrow profile-dropdown " aria-labelledby="Preview">
                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h5 class="text-overflow"><small>Welcome {{ Auth::user()->fname }}!</small> </h5>
                </div>

                <!-- item-->
                <a href="{{ route('profile') }}" class="dropdown-item notify-item">
                    <i class="zmdi zmdi-account-circle"></i> <span>Profile</span>
                </a>

                <!-- item-->
                <a href="/logout" class="dropdown-item notify-item">
                    <i class="zmdi zmdi-power"></i> <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>
</nav>