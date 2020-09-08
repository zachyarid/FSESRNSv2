<div class="sidebar-inner slimscrollleft">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <ul>
            <li class="text-muted menu-title">Navigation</li>

            <li class="has_sub">
                <a href="{{ route('home') }}" class="waves-effect"><i class="zmdi zmdi-view-dashboard"></i><span>Dashboard</span></a>
            </li>

            <li class="has_sub">
                <a href="javascript:void(0);" class="waves-effect subdrop"><i class="zmdi zmdi-eye"></i><span>View Report</span><span class="menu-arrow"></span></a>
                <ul class="list-unstyled" style="display:block;">
                    @forelse (\Auth::user()->reportSubscriptions() as $s)
                        <li><a style="font-size:15px;" href="{{ route('report.view', $s->id) }}" class="waves-effect">{{ $s->service->name }}<br /><span style="font-size:10px;">{{ $s->group->name }}</span></a></li>
                    @empty
                    <li><a href="{{ route('subscriptions.list') }}" class="waves-effect">Subscribe Now!</a></li>
                    @endforelse
                </ul>
            </li>

            @if (count(\Auth::user()->monitorSubscriptions()))
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect subdrop"><i class="ion-monitor"></i><span>Manage Monitors</span><span class="menu-arrow"></span></a>
                    <ul class="list-unstyled" style="display:block;">
                        @foreach (\Auth::user()->monitorSubscriptions() as $ms)
                            <li><a style="font-size:15px;" href="{{ route('monitor.view', $ms->id) }}" class="waves-effect">{{ $ms->service->name }}<br /><span style="font-size:10px;">{{ $ms->group->name }}</span></a></li>
                        @endforeach
                    </ul>
                </li>
            @endif

            @if (count(\Auth::user()->sharedSubscriptions))
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect subdrop"><i class="zmdi zmdi-accounts"></i><span>Shared Subscriptions</span><span class="menu-arrow"></span></a>
                    <ul class="list-unstyled" style="display:block;">
                        @foreach (\Auth::user()->sharedSubscriptions as $ss)
                            <li><a style="font-size:15px;" href="{{ route('report.view', $ss->id) }}" class="waves-effect">{{ $ss->service->name }}<br /><span style="font-size:10px;">{{ $ss->group->name }}</span></a></li>
                        @endforeach
                    </ul>
                </li>
            @endif

            <li class="has_sub">
                <a href="javascript:void(0);" class="waves-effect subdrop"><i class="zmdi zmdi-edit"></i><span>Manage</span><span class="menu-arrow"></span></a>
                <ul class="list-unstyled" style="display:block;">
                    <li><a href="{{ route('groups.index') }}" class="waves-effect"><i class="fa fa-folder"></i>Groups</a></li>
                    <li><a href="{{ route('payments.index') }}" class="waves-effect"><i class="fa fa-money"></i>Payments</a></li>
                    <li><a href="{{ route('special-payments.index') }}" class="waves-effect"><i class="fa fa-exclamation-circle"></i>Special Payments</a></li>
                    <li><a href="{{ route('subscriptions.index') }}" class="waves-effect"><i class="zmdi zmdi-collection-case-play"></i>Subscriptions</a></li>
                    <li><a href="{{ route('useraccess.index') }}" class="waves-effect"><i class="fa fa-group"></i>User Access</a></li>

                </ul>
            </li>

            @if (\Auth::user()->roles->contains($role = \App\Role::where('name', 'Administrator')->first()->id))
            <li class="has_sub">
                <a href="javascript:void(0);" class="waves-effect"><i class="zmdi zmdi-settings"></i><span>Admin</span><span class="menu-arrow"></span></a>
                <ul class="list-unstyled">
                    <li><a href="{{ route('admin.groups') }}" class="waves-effect"><i class="fa fa-folder"></i>Groups</a></li>
                    <li><a href="{{ route('admin.payments') }}" class="waves-effect"><i class="fa fa-money"></i>Payments</a></li>
                    <li><a href="{{ route('admin.subscriptions') }}" class="waves-effect"><i class="zmdi zmdi-collection-case-play"></i>Subscriptions</a></li>
                    <li><a href="{{ route('admin.users') }}" class="waves-effect"><i class="fa fa-group"></i>Users</a></li>
                </ul>
            </li>
            @endif

        </ul>
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -->
    <div class="clearfix"></div>
</div>