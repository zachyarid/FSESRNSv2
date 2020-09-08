@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('useraccess.store') }}">
                @csrf
                <div class="form-group row{{ $errors->has('subscription') ? ' has-danger' : '' }}">
                    <label for="subscription" class="col-sm-2 form-control-label">Subscription</label>
                    <div class="col-sm-5">
                        <select class="select2 form-control{{ $errors->has('subscription') ? ' form-control-danger' : '' }}" id="subscription" name="subscription">
                            <option selected disabled>Select a subscription</option>
                            @if (Auth::user()->subscriptions->count() > 0)
                                @foreach (Auth::user()->subscriptions as $s)
                                    @if ($s->id == old('subscription'))
                                        <option selected value="{{ $s->id }}">{{ $s->service->name }} for {{ $s->group->name }}</option>
                                    @else
                                        <option value="{{ $s->id }}">{{ $s->service->name }} for {{ $s->group->name }}</option>
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

                <div class="form-group row{{ $errors->has('username') ? ' has-danger' : '' }}">
                    <label for="username" class="col-sm-2 form-control-label">Username</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control{{ $errors->has('username') ? ' form-control-danger' : '' }}" value="{{ old('username') }}" id="username" name="username">
                    </div>

                    @if ($errors->has('username'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Add User Access</button>
                    </div>
                </div>
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