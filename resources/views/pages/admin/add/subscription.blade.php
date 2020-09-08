@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <form method="post" action="{{ route('admin.subscription.add') }}">
                    @csrf

                    <div class="form-group row{{ $errors->has('service_id') ? ' has-danger' : '' }}">
                        <label for="access_key" class="col-sm-3 form-control-label">Service</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control" name="service_id">
                                <option selected disabled>Select a service</option>
                                @if ($services->count() > 0)
                                    @foreach ($services as $s)
                                        @if (old('service_id') == $s->id)
                                            <option selected value="{{ $s->id }}">{{ $s->name }} - ${{ number_format($s->base_cost, 0) }}</option>
                                        @else
                                            <option value="{{ $s->id }}">{{ $s->name }} - ${{ number_format($s->base_cost, 0) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option>No groups added!</option>
                                @endif
                            </select>
                        </div>

                        @if ($errors->has('service_id'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('service_id') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('monthly_rate') ? ' has-danger' : '' }}">
                        <label for="monthly_rate" class="col-sm-3 form-control-label">Override Monthly Rate</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control{{ $errors->has('monthly_rate') ? ' form-control-danger' : '' }}" id="monthly_rate" name="monthly_rate" value="{{ old('monthly_rate') }}" />
                        </div>

                        @if ($errors->has('monthly_rate'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('monthly_rate') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('group_id') ? ' has-danger' : '' }}">
                        <label for="group_id" class="col-sm-3 form-control-label">Group</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control{{ $errors->has('group_id') ? ' form-control-danger' : '' }}" id="group_id" name="group_id">
                                <option selected disabled>Select a group</option>
                                @if ($groups->count() > 0)
                                    @foreach ($groups as $g)
                                        @if (old('group_id') == $g->id)
                                            <option selected value="{{ $g->id }}">{{ $g->name }} {{ empty($g->owner) ? '' : '('.$g->owner.')' }}</option>
                                        @else
                                            <option value="{{ $g->id }}">{{ $g->name }} {{ empty($g->owner) ? '' : '('.$g->owner.')' }}</option>
                                        @endif
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

                    <div class="form-group row">
                        <div class="col-md-3">
                            <button class="btn btn-success">Start Service!</button>
                        </div>
                    </div>
                </form>
            </div>
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