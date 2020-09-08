@extends('layouts.template')

@section('content')
    <p>
        <button onclick="window.location = '{{ route('admin.subscription.add') }}'" class="btn btn-default">Add A Subscription</button>
    </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h5 class="page-title">Subscriptions</h5>
                <table id="my_subscriptions" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Group Name</th>
                        <th>Subscription</th>
                        <th>Monthly Cost</th>
                        <th>Account Status</th>
                        <th>Next Due Date</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @if (count($subscriptions) > 0)
                        @foreach($subscriptions as $s)
                            <tr>
                                <td>{{ $s->id }}</td>
                                <td>{{ $s->group->name }}</td>
                                <td>{{ $s->service->name }}</td>
                                <td>$ {{ number_format($s->monthly_cost, 0) }}</td>
                                <td> {{ $s->stat->string }}</td>
                                <td> {{ \Carbon\Carbon::parse($s->next_due_date)->format('M d, Y') }}</td>
                                <td><button class="btn btn-info" onclick="window.location = '{{ route('admin.subscriptions.manage', $s->id) }}'">Manage</button></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No subscriptions!</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection