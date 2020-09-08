@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <button class="btn btn-default" onclick="window.location = '{{ route('subscriptions.list') }}';">Quick Add Subscription</button>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h5 class="page-title">View Reports</h5>
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="my_reports" class="table table-striped table-bordered dataTable no-footer"
                                   role="grid" aria-describedby="datatable_info">
                                <thead>
                                <tr>
                                    <th>Subscription</th>
                                    <th>Group Name</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                @if (count($subscriptions) > 0)
                                    @foreach($subscriptions as $s)
                                        <tr>
                                            <td>{{ $s->service->name }}</td>
                                            <td>{{ $s->group->name }}</td>
                                            <td>
                                                @if ($s->service->type == 'fbo')
                                                    <button class="btn btn-info" onclick="goToReport({{ $s->id }})">View</button>
                                                @elseif ($s->service->type == 'flight')
                                                    <button class="btn btn-info" onclick="goToReport({{ $s->id }})">View</button>
                                                @elseif ($s->service->type == 'combo')
                                                    <button class="btn btn-info" onclick="goToReport({{ $s->id }})">View</button>
                                                    <button class="btn btn-info" onclick="goToReport({{ $s->id }})">View</button>
                                                @elseif ($s->service->type == 'aircraft')
                                                    <button class="btn btn-info" onclick="goToReport({{ $s->id }})">View</button>
                                                @elseif ($s->service->type == 'monitor')
                                                    <button class="btn btn-info" onclick="window.location = '/monitor/{{ $s->id }}'">View</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">No subscriptions!</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h5 class="page-title">Report Access Granted</h5>
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="my_useraccess" class="table table-striped table-bordered dataTable no-footer"
                                   role="grid" aria-describedby="datatable_info">
                                <thead>
                                <tr>
                                    <th>Subscription</th>
                                    <th>Group</th>
                                    <th>Granted By</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                @if (count($grantedAccess) > 0)
                                    @foreach($grantedAccess as $g)
                                        <tr>
                                            <td>{{ $g->service->name }}</td>
                                            <td>{{ $g->group->name }}</td>
                                            <td>{{ $g->user->username }}</td>
                                            <td>
                                                @if ($g->service->type == 'fbo')
                                                    <button class="btn btn-info" onclick="goToReport({{ $g->id }})">View</button>
                                                @elseif ($g->service->type == 'flight')
                                                    <button class="btn btn-info" onclick="goToReport({{ $g->id }})">View</button>
                                                @elseif ($g->service->type == 'combo')
                                                    <button class="btn btn-info" onclick="goToReport({{ $g->id }})">View</button>
                                                    <button class="btn btn-info" onclick="goToReport({{ $g->id }})">View</button>
                                                @elseif ($g->service->type == 'aircraft')
                                                    <button class="btn btn-info" onclick="goToReport({{ $g->id }})">View</button>
                                                @elseif ($g->service->type == 'monitor')
                                                    <button class="btn btn-info" onclick="window.location = '/monitor/{{ $g->id }}'">View</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">No additional user access granted!</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-source')
    <script>
        function goToReport(subscription)
        {
            window.location = "/report/" + subscription;
        }
    </script>
@endsection
