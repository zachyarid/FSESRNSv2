@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('groups.update', $group->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group row{{ $errors->has('group_name') ? ' has-danger' : '' }}">
                    <label for="group_name" class="col-sm-2 form-control-label">Group Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('group_name') ? ' form-control-danger' : '' }}" id="group_name" name="group_name" value="{{ $group->name }}">
                    </div>

                    @if ($errors->has('group_name'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('group_name') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('group_owner') ? ' has-danger' : '' }}">
                    <label for="group_owner" class="col-sm-2 form-control-label">Owner</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('group_owner') ? ' form-control-danger' : '' }}" id="group_owner" name="group_owner" value="{{ $group->owner }}">
                    </div>

                    @if ($errors->has('group_owner'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('group_owner') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('group_type') ? ' has-danger' : '' }}">
                    <label for="group_type" class="col-sm-2 form-control-label">Type</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('group_type') ? ' form-control-danger' : '' }}" id="group_type" name="group_type" value="{{ $group->type }}" readonly disabled>
                    </div>

                    @if ($errors->has('group_type'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('group_type') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row{{ $errors->has('access_key') ? ' has-danger' : '' }}">
                    <label for="access_key" class="col-sm-2 form-control-label">Access Key</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control{{ $errors->has('access_key') ? ' form-control-danger' : '' }}" id="access_key" name="access_key" value="{{ $group->access_key }}">
                    </div>

                    @if ($errors->has('access_key'))
                        <div class="col-sm-4">
                            <span class="text-danger">
                                <strong>{{ $errors->first('access_key') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Edit Group</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection