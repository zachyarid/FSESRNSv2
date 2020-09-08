@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('subscriptions.store') }}">
                @csrf

                <div class="form-group row{{ $errors->has('service_id') ? ' has-danger' : '' }}">
                    <input type="hidden" value="{{ $selectedService->id }}" name="service_id" />
                    <label for="access_key" class="col-sm-3 form-control-label">Service</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('service_id') ? ' form-control-danger' : '' }}" value="{{ $selectedService->name }}" disabled readonly>
                    </div>

                    @if ($errors->has('service_id'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('service_id') }}</strong>
                            </span>
                        </div>
                    @endif

                    @if ($selectedService->id == 14 && empty(Auth::user()->fse_password))
                        <div class="col-sm-4">
                            <span class="text-warning">
                                <strong>In order for our FBO Auto Resupply service to work, we will need your FSE password so that we may interact with the FSE Game World server. Please click <a href="{{ route('profile') }}">here</a> to set it.</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('payment_amount') ? ' has-danger' : '' }}">
                    <label for="payment_amount" class="col-sm-3 form-control-label">Payment Amount</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control{{ $errors->has('payment_amount') ? ' form-control-danger' : '' }}" id="payment_amount" name="payment_amount" value="{{ $selectedService->base_cost }}" readonly>
                    </div>

                    @if ($errors->has('payment_amount'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('payment_amount') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('group_id') ? ' has-danger' : '' }}">
                    <label for="group_id" class="col-sm-3 form-control-label">Group</label>
                    <div class="col-sm-4">
                        <select class="select2 form-control{{ $errors->has('group_id') ? ' form-control-danger' : '' }}" id="group_id" name="group_id" onchange="populateGroupPay()">
                            <option selected disabled>Select a group</option>
                            @if (Auth::user()->groups->count() > 0)
                                @foreach (Auth::user()->groups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            @else
                                <option>No groups added!</option>
                            @endif
                        </select>
                    </div>

                    @if ($errors->has('group_id'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('group_id') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('pmt_rec_from') ? ' has-danger' : '' }}">
                    <label for="pmt_rec_from" class="col-sm-3 form-control-label">Payment Received From</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('pmt_rec_from') ? ' form-control-danger' : '' }}" id="pmt_rec_from" name="pmt_rec_from" value="{{ old('pmt_rec_from') }}">
                    </div>

                    @if ($errors->has('pmt_rec_from'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('pmt_rec_from') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Start Service!</button>
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

        function populateGroupPay()
        {
            var text = $("#group_id option:selected").text();
            document.getElementById("pmt_rec_from").value = text;
        }
    </script>
@endsection