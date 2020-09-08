@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('useraccess.create') }}'" class="btn btn-default">Add User Access</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="my_useraccess" class="table table-striped table-bordered dataTable no-footer"
                                   role="grid" aria-describedby="datatable_info">
                                <thead>
                                <tr>
                                    <th>Granted To</th>
                                    <th>Group</th>
                                    <th>Subscription</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                @if (count($useraccess) > 0)
                                    @foreach($useraccess as $a)
                                        <tr>
                                            <td>{{ App\User::find($a->pivot->user_id)->username }}</td>
                                            <td>{{ $a->group->name }}</td>
                                            <td>{{ $a->service->name }}</td>
                                            <td>
                                                <button class="btn btn-danger" onclick="doDelete({{ $a->id }} , {{ $a->pivot->user_id }})">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">No additional user access added!</td>
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
        function doDelete(sid, uid)
        {
            var result = confirm('Are you sure you want to delete this user access?');

            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '/useraccess/' + sid + '/' + uid,
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        console.log(data);
                        location.reload();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        }
    </script>
@endsection