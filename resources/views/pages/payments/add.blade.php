@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('payments.store') }}">
                @csrf

                <div class="form-group row">
                    <div class="col-md-12">
                        <p>To assist with payment tracking, please place the account number for the service you are paying in the Comment section of the Game World payment form</p>
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('subscription') ? ' has-danger' : '' }}">
                    <label for="subscription" class="col-sm-2 form-control-label">Subscription</label>
                    <div class="col-sm-5">
                        <select class="select2 form-control{{ $errors->has('subscription') ? ' form-control-danger' : '' }}" id="subscription" name="subscription" onchange="">
                            <option selected disabled>Select a subscription</option>
                            @if (Auth::user()->subscriptions->count() > 0)
                                @foreach (Auth::user()->subscriptions as $s)
                                    @if ($s->id == old('subscription'))
                                        <option selected value="{{ $s->id }}">Acct #{{ $s->id }}, {{ $s->service->name }} ({{ $s->group->name }}) : ${{ number_format($s->monthly_cost, 0) }} per month</option>
                                    @else
                                        <option value="{{ $s->id }}">Acct #{{ $s->id }}, {{ $s->service->name }} ({{ $s->group->name }}) : ${{ number_format($s->monthly_cost, 0) }} per month</option>
                                    @endif
                                @endforeach
                            @else
                                <option>No subscriptions added!</option>
                            @endif
                        </select>
                    </div>

                    @if ($errors->has('subscription'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('subscription') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('payment_amount') ? ' has-danger' : '' }}">
                    <label for="payment_amount" class="col-sm-2 form-control-label">Payment Amount</label>
                    <div class="col-sm-5">
                        <input type="number" class="form-control{{ $errors->has('payment_amount') ? ' form-control-danger' : '' }}" value="{{ old('payment_amount') }}" id="payment_amount" name="payment_amount" />
                    </div>

                    @if ($errors->has('payment_amount'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('payment_amount') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('payment_group') ? ' has-danger' : '' }}">
                    <label for="payment_group" class="col-sm-2 form-control-label">Payment Group</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control{{ $errors->has('payment_group') ? ' form-control-danger' : '' }}" value="{{ old('payment_group') }}" id="payment_group" name="payment_group" />
                    </div>

                    @if ($errors->has('payment_group'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('payment_group') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Make Payment</button>
                    </div>
                </div>

                <p style="color:red;"><i>Make sure</i> that you have sent your payment to <u>Starfleet Financial</u> <b>BEFORE</b> clicking the 'Start Service!' button.</p>
            </form>
        </div>
    </div>
@endsection

@section('script-source')
    <script>
        $(document).ready(function () {
            $(".select2").select2();
        });
    </script>
@endsection