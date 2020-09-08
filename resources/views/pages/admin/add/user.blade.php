@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <form method="post" action="{{ route('admin.users.add') }}">
                    @csrf

                    <div class="form-group row{{ $errors->has('first_name') ? ' has-danger' : '' }}">
                        <label for="first_name" class="col-sm-3 form-control-label">First Name</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control{{ $errors->has('first_name') ? ' form-control-danger' : '' }}" id="first_name" name="first_name" value="{{ old('first_name') }}" />
                        </div>

                        @if ($errors->has('first_name'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('last_name') ? ' has-danger' : '' }}">
                        <label for="last_name" class="col-sm-3 form-control-label">Last Name</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control{{ $errors->has('last_name') ? ' form-control-danger' : '' }}" id="last_name" name="last_name" value="{{ old('last_name') }}" />
                        </div>

                        @if ($errors->has('last_name'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('first_name') ? ' has-danger' : '' }}">
                        <label for="email" class="col-sm-3 form-control-label">Email</label>
                        <div class="col-sm-4">
                            <input type="email" class="form-control{{ $errors->has('email') ? ' form-control-danger' : '' }}" id="email" name="email" value="{{ old('email') }}" />
                        </div>

                        @if ($errors->has('email'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('username') ? ' has-danger' : '' }}">
                        <label for="username" class="col-sm-3 form-control-label">FSE Username</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control{{ $errors->has('username') ? ' form-control-danger' : '' }}" id="username" name="username" value="{{ old('username') }}" />
                        </div>

                        @if ($errors->has('username'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row{{ $errors->has('personal_ak') ? ' has-danger' : '' }}">
                        <label for="personal_ak" class="col-sm-3 form-control-label">Personal Access Key</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control{{ $errors->has('personal_ak') ? ' form-control-danger' : '' }}" id="personal_ak" name="personal_ak" value="{{ old('personal_ak') }}" />
                        </div>

                        @if ($errors->has('personal_ak'))
                            <div class="col-sm-4">
                                <span class="text-danger">
                                    <strong>{{ $errors->first('personal_ak') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <button class="btn btn-success">Add User</button>
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