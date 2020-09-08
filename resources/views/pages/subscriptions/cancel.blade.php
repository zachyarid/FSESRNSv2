@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('subscriptions.doCancel', $subscription->id) }}">
                @csrf
                @method('delete')

                <div class="form-group row">
                    <div class="col-lg-12">
                        <p class="text-danger">We are sorry to see you go! Remember that we do not offer refunds for partial month subscriptions and if you wish to resume services, your account might be subject to a reconnect fee.</p>
                    </div>
                </div>

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
                    <div class="col-md-3">
                        <button class="btn btn-danger">Cancel Subscription</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection