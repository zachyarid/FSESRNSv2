@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <div class="form-group row">
                <label for="username" class="col-sm-2 form-control-label">Username</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-sm-2 form-control-label">Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->fname }} {{ $user->lname }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-sm-2 form-control-label">Email</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="personal_key" class="col-sm-2 form-control-label">Personal Access Key</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="personal_key" name="personal_key" value="{{ $user->personal_key }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="service_key" class="col-sm-2 form-control-label">Service Key</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="service_key" name="service_key" value="{{ $user->service_key }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 form-control-label">Status</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="status" name="status" value="{{ $user->stat->string }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                @if ($user->status == 1)
                    <div class="col-md-2">
                        <button class="btn btn-danger" onclick="window.location = '{{ route('admin.users.ban', $user->id) }}'">Ban User</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-warning" onclick="window.location = '{{ route('admin.users.inactive', $user->id) }}'">Set User Inactive</button>
                    </div>
                @elseif ($user->status == 2)
                    <div class="col-md-2">
                        <button class="btn btn-danger" onclick="window.location = '{{ route('admin.users.ban', $user->id) }}'">Ban User</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success" onclick="window.location = '{{ route('admin.users.active', $user->id) }}'">Set User Active</button>
                    </div>
                @elseif ($user->status == 3)
                    <div class="col-md-2">
                        <button class="btn btn-success" onclick="window.location = '{{ route('admin.users.active', $user->id) }}'">Re-Activate User</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-warning" onclick="window.location = '{{ route('admin.users.inactive', $user->id) }}'">Set User Inactive</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card-box">
            <h5>Report Access Granted</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Subscription</th>
                        <th>Granted By</th>
                        <th>Granted On</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($granted as $sub)
                        <tr>
                            <td>{{ $sub->service->name }}</td>
                            <td>{{ $sub->user->username }}</td>
                            <td>{{ $sub->pivot->created_at->format('M d, Y H:i:s') }}</td>
                            <td><button class="btn btn-danger" onclick="doDelete({{ $sub->id }}, {{ $sub->pivot->user_id }})">Revoke</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"></td></tr>
                    @endforelse
                    </tbody>
                </table>
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