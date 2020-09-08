@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box">
            <form method="post" action="{{ route('groups.store') }}">
                @csrf
                <div class="form-group row">
                    <label for="access_key" class="col-sm-2 form-control-label">Access Key</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="access_key" name="access_key">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <p class="font-italic">*We will request group information from the FSE server</p>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button class="btn btn-success">Add Group</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection