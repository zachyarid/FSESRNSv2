@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('admin.users.add') }}'" class="btn btn-default">Add A User</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="users" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Access Key</th>
                        <th>Status</th>
                        <th>Active Subscriptions</th>
                        <th>Number of Groups</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @if (count($users) > 0)
                        @foreach($users as $u)
                            <tr>
                                <td>{{ $u->username }}</td>
                                <td>{{ $u->fname }} {{ $u->lname }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->personal_key }}</td>
                                <td>{{ $u->stat->string }}</td>
                                <td>{{ count($u->subscriptions) }}</td>
                                <td>{{ count($u->groups) }}</td>
                                <td><button class="btn btn-info" onclick="window.location = '{{ route('admin.users.view', $u->id) }}'">View</button></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">No users added!</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script-source')
    <script>
        $(document).ready(function () {
            $('#users').DataTable({
                responsive: true,
                bLengthChange: false,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });
        });
    </script>
@endsection