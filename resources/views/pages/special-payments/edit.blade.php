@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('special-payments.update', ['specialPayment' => $specialPayment->id]) }}">
                @csrf
                @method('PUT')

                <div class="form-group row">
                    <label for="payment_amount" class="col-sm-2 form-control-label">Payment Amount</label>
                    <div class="col-sm-2">
                        <input type="number" class="form-control{{ $errors->has('payment_amount') ? ' form-control-danger' : '' }}" id="payment_amount" name="payment_amount" value="{{ $specialPayment->amount }}">
                    </div>

                    @if ($errors->has('payment_amount'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('payment_amount') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <label for="payment_type" class="col-sm-2 form-control-label">Special Payment Type</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control{{ $errors->has('payment_type') ? ' form-control-danger' : '' }}" id="payment_type" name="payment_type" value="{{ $specialPayment->type }}">
                    </div>

                    @if ($errors->has('payment_type'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('payment_type') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('group_id') ? ' has-danger' : '' }}">
                    <label for="group_id" class="col-sm-2 form-control-label">Group</label>
                    <div class="col-sm-2">
                        <select class="select2 form-control{{ $errors->has('group_id') ? ' form-control-danger' : '' }}" id="group_id" name="group_id">
                            @forelse (Auth::user()->groups as $g)
                                @if ($g->type == 'Personal')
                                    <option selected value="{{ $g->id }}">{{ $g->name }}</option>
                                @else
                                    <option {{ $specialPayment->group_id == $g->id ? 'selected' : '' }} value="{{ $g->id }}">{{ $g->name }}</option>
                                @endif
                            @empty
                                <option>No groups added!</option>
                            @endforelse
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

                <div class="form-group row">
                    <label for="frequency" class="col-sm-2 form-control-label">Frequency</label>
                    <div class="col-sm-2">
                        <select class="select2 form-control{{ $errors->has('frequency') ? ' form-control-danger' : '' }}" id="frequency" name="frequency">
                            <option disabled selected>Select a frequency</option>
                            <option value="1" {{ $specialPayment->frequency == 1 ? 'selected' : '' }}>Daily</option>
                            <option value="7" {{ $specialPayment->frequency == 7 ? 'selected' : '' }}>Weekly</option>
                            <option value="14" {{ $specialPayment->frequency == 14 ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="30" {{ $specialPayment->frequency == 30 ? 'selected' : '' }}>Monthly</option>
                            <option value="90" {{ $specialPayment->frequency == 90 ? 'selected' : '' }}>Quarterly</option>
                            <option value="180" {{ $specialPayment->frequency == 180 ? 'selected' : '' }}>Bi-annually</option>
                            <option value="365" {{ $specialPayment->frequency == 365 ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>

                    @if ($errors->has('frequency'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('frequency') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <label for="comments" class="col-sm-2 form-control-label">Comments</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="comments" name="comments" rows="3">{{ $specialPayment->comment }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Edit Special Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
        });
    </script>
@endsection