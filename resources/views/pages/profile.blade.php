@extends('layouts.template')

@section('content')
    <div class="card-box">
        <form method="post" action="{{ route('profile') }}">
            @csrf

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">First Name</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="fname" value="{{ $user->fname }}">
                </fieldset>

                @if ($errors->has('fname'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('fname') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">Last Name</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="lname" value="{{ $user->lname }}">
                </fieldset>

                @if ($errors->has('lname'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('lname') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" value="{{ $user->email }}">
                </fieldset>

                @if ($errors->has('email'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">Username</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="username" value="{{ $user->username }}" readonly>
                </fieldset>
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                </fieldset>

                @if ($errors->has('password'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputPassword1">Confirm Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password_confirmation">
                </fieldset>

                @if ($errors->has('password_confirmation'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">Access Key</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="personal_key" value="{{ $user->personal_key }}">
                </fieldset>

                @if ($errors->has('personal_key'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('personal_key') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputEmail1">Service Key</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="service_key" value="{{ $user->service_key }}">
                </fieldset>

                @if ($errors->has('service_key'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('service_key') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <div class="row">
                <fieldset class="form-group col-md-4">
                    <label for="exampleInputPassword2">FSE Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword2" name="fse_password" value="{{ isset($user->fse_password) ? decrypt($user->fse_password) : '' }}">
                    <small class="text-muted">
                        We need this value if you wish to interact with the FSE Game World site from here.
                    </small>
                </fieldset>

                @if ($errors->has('fse_password'))
                    <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('fse_password') }}</strong>
                            </span>
                    </div>
                @endif
            </div>

            <fieldset class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
            </fieldset>
        </form>
    </div>
@endsection

@section('script-source')

@endsection
