@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('payments.create') }}'" class="btn btn-default">Add A Payment</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="my_payments" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Subscription</th>
                        <th>Group</th>
                        <th>Amount</th>
                        <th>From</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @if (count($payments) > 0)
                        @foreach($payments as $p)
                            <tr>
                                <td>{{ $p->subscription->id }}</td>
                                <td>{{ $p->subscription->service->name }}</td>
                                <td>{{ $p->subscription->group->name }}</td>
                                <td>$ {{ number_format($p->amount, 0) }}</td>
                                <td>{{ $p->payer }}</td>
                                <td>{{ $p->status }}</td>
                                <td><button class="btn btn-success" onclick="window.location = '{{ route('admin.payment.confirm', $p->id) }}'">Confirm</button></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">No payments!</td>
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
            $('#my_payments').DataTable({
                responsive: true,
                bLengthChange: true,
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