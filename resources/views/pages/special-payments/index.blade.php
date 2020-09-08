@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('special-payments.create') }}'" class="btn btn-default">Add A Special Payment</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="my_special_payments" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Group</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Frequency</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($specialPayments as $sp)
                        <tr>
                            <td>{{ $sp->group->name }}</td>
                            <td>{{ $sp->type }}</td>
                            <td>${{ number_format($sp->amount,2) }}</td>
                            <td>{{ $sp->frequency }} days</td>
                            <td>
                                <button class="btn btn-default" onclick="window.location = '{{ route('special-payments.show', ['specialPayment' => $sp->id]) }}'">View</button>
                                <button class="btn btn-info m-l-5" onclick="window.location = '{{ route('special-payments.edit', ['specialPayment' => $sp->id]) }}'">Edit</button>
                                <button class="btn btn-danger m-l-5">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No special payments added!</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#my_special_payments').DataTable({
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