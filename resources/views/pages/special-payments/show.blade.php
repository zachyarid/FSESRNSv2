@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form>
                <div class="form-group row">
                    <label for="payment_amount" class="col-sm-2 form-control-label">Payment Amount</label>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="payment_amount" name="payment_amount" value="{{ $specialPayment->amount }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payment_type" class="col-sm-2 form-control-label">Special Payment Type</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="payment_type" name="payment_type" value="{{ $specialPayment->type }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="group_id" class="col-sm-2 form-control-label">Group</label>
                    <div class="col-sm-2">
                        <select class="select2 form-control" id="group_id" name="group_id">
                            @forelse (Auth::user()->groups as $g)
                                <option {{ $specialPayment->group_id == $g->id ? 'selected disabled' : '' }} value="{{ $g->id }}">{{ $g->name }}</option>
                            @empty
                                <option disabled selected>No groups added!</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="frequency" class="col-sm-2 form-control-label">Frequency</label>
                    <div class="col-sm-2">
                        <select class="select2 form-control" id="frequency" name="frequency">
                            <option value="1" {{ $specialPayment->frequency == 1 ? 'selected disabled' : '' }}>Daily</option>
                            <option value="7" {{ $specialPayment->frequency == 7 ? 'selected disabled' : '' }}>Weekly</option>
                            <option value="14" {{ $specialPayment->frequency == 14 ? 'selected disabled' : '' }}>Bi-Weekly</option>
                            <option value="30" {{ $specialPayment->frequency == 30 ? 'selected disabled' : '' }}>Monthly</option>
                            <option value="90" {{ $specialPayment->frequency == 90 ? 'selected disabled' : '' }}>Quarterly</option>
                            <option value="180" {{ $specialPayment->frequency == 180 ? 'selected disabled' : '' }}>Bi-annually</option>
                            <option value="365" {{ $specialPayment->frequency == 365 ? 'selected disabled' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="comments" class="col-sm-2 form-control-label">Comments</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="comments" name="comments" rows="3" readonly>{{ $specialPayment->comment }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2({disabled:true});
        });
    </script>
@endsection