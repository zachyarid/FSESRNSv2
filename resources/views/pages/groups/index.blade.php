@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('groups.create') }}'" class="btn btn-default">Add A Group</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="my_groups" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Access Key</th>
                            <th>Type</th>
                            <th>Active Subscription</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($groups) > 0)
                            @foreach($groups as $g)
                                <tr>
                                    <td>{{ $g->name }}</td>
                                    <td>{{ $g->access_key }}</td>
                                    <td>{{ $g->type }}</td>
                                    <td>{{ count($g->subscription) ? 'Acct #' . $g->subscription->id : 'No' }}</td>
                                    <td>
                                        @if ($g->type == 'Group')
                                            <button class="btn btn-info" onclick="window.location = '{{ route('groups.edit', $g->id) }}'">Edit</button>
                                            <button class="btn btn-danger" onclick="doDelete({{ $g->id }})">Remove</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">No groups added!</td>
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
            $('#my_groups').DataTable({
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

        function doDelete(id)
        {
            var result = confirm('Are you sure you want to delete this group?');

            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '/groups/' + id,
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        console.log(data);

                        if (!data.success) {
                            toastr["error"](data.message,
                                "User Action Required", {
                                    "closeButton": true,
                                    "debug": false,
                                    "newestOnTop": false,
                                    "progressBar": false,
                                    "positionClass": "toast-top-right",
                                    "preventDuplicates": false,
                                    "onclick": null,
                                    "showDuration": "10000",
                                    "hideDuration": "5000",
                                    "timeOut": "10000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                });
                        } else {
                            location.reload();
                        }
                    },
                    error: function (data) {
                        console.log(data);

                        switch (data.status) {
                            case 403:
                                toastr["error"]("You do not have permission to perform that action",
                                    "Unathorized action", {
                                    "closeButton": true,
                                    "debug": false,
                                    "newestOnTop": false,
                                    "progressBar": false,
                                    "positionClass": "toast-top-right",
                                    "preventDuplicates": false,
                                    "onclick": null,
                                    "showDuration": "5000",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                });

                                break;
                            default:
                                toastr.info('Unrecognized error');
                        }
                    }
                });
            }
        }
    </script>
@endsection