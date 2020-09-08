@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <div class="form-group row">
                <label for="account_number" class="col-sm-2 form-control-label">Account Number</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="account_number" name="account_number" value="{{ $subscription->id }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="group" class="col-sm-2 form-control-label">Group</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="group" name="group" value="{{ $subscription->group->name }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="subscription" class="col-sm-2 form-control-label">Subscription</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="subscription" name="subscription" value="{{ $subscription->service->name }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="monthly_cost" class="col-sm-2 form-control-label">Monthly Cost</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="monthly_cost" name="monthly_cost" value="{{ $subscription->monthly_cost }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 form-control-label">Status</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="status" name="status" value="{{ $subscription->stat->string }}" readonly disabled />
                </div>
            </div>

            <div class="form-group row">
                @if ($subscription->status !== 1)
                    <div class="col-md-2">
                        <button class="btn btn-success" onclick="window.location = '{{ route('admin.subscriptions.reactivate', $subscription->id) }}'">Re-Activate Subscription</button>
                    </div>
                @else
                    <div class="col-md-2">
                        <button class="btn btn-danger" onclick="window.location = '{{ route('admin.subscriptions.cancel', $subscription->id) }}'">Cancel Subscription</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection