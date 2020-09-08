@extends('layouts.template')

@section('content')
    <div class="col-lg-12">
        <div class="card-box table-responsive">
            <h5 class="page-title">FBO Tracking Services</h5>
            <table class="table table-striped table-bordered no-footer">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Monthly Cost</th>
                        <th>Sign Up</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($fbo as $f)
                        <tr>
                            <td>{{ $f->name }}</td>
                            @if ($f->base_cost == 0)
                                <td><strike>$0</strike> <strong>FREE!</strong> </td>
                            @else
                                <td>$ {{ number_format($f->base_cost, 0) }}</td>
                            @endif
                            <td><button class="btn btn-success" onclick="window.location = '{{ route('subscriptions.create', $f->id) }}'">Subscribe!</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No FBO Services found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-box table-responsive">
            <h5 class="page-title">Flight Tracking Services</h5>
            <table class="table table-striped table-bordered no-footer">
                <thead>
                <tr>
                    <th>Service</th>
                    <th>Monthly Cost</th>
                    <th>Sign Up</th>
                </tr>
                </thead>

                <tbody>
                    @forelse($flight as $f)
                        <tr>
                            <td>{{ $f->name }}</td>
                            @if ($f->base_cost == 0)
                                <td><strike>$0</strike> <strong>FREE!</strong> </td>
                            @else
                                <td>$ {{ number_format($f->base_cost, 0) }}</td>
                            @endif
                            <td><button class="btn btn-success" onclick="window.location = '{{ route('subscriptions.create', $f->id) }}'">Subscribe!</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No Flight Services found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-box table-responsive">
            <h5 class="page-title">Combo Tracking Services</h5>
            <table class="table table-striped table-bordered no-footer">
                <thead>
                <tr>
                    <th>Service</th>
                    <th>Monthly Cost</th>
                    <th>Sign Up</th>
                </tr>
                </thead>

                <tbody>
                    @forelse($combo as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            @if ($c->base_cost == 0)
                                <td><strike>$0</strike> <strong>FREE!</strong> </td>
                            @else
                                <td>$ {{ number_format($c->base_cost, 0) }}</td>
                            @endif
                            <td><button class="btn btn-success" onclick="window.location = '{{ route('subscriptions.create', $f->id) }}'">Subscribe!</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No Combo Services found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-box table-responsive">
            <h5 class="page-title">Aircraft Management Services</h5>
            <table class="table table-striped table-bordered no-footer">
                <thead>
                <tr>
                    <th>Service</th>
                    <th>Monthly Cost</th>
                    <th>Sign Up</th>
                </tr>
                </thead>

                <tbody>
                    @forelse($aircraft as $a)
                        <tr>
                            <td>{{ $a->name }}</td>
                            @if ($a->base_cost == 0)
                                <td><strike>$0</strike> <strong>FREE!</strong> </td>
                            @else
                                <td>$ {{ number_format($a->base_cost, 0) }}</td>
                            @endif
                            <td><button class="btn btn-success" onclick="window.location = '{{ route('subscriptions.create', $f->id) }}'">Subscribe!</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No Aircraft Services found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-box table-responsive">
            <h5 class="page-title">FBO Services</h5>
            <table class="table table-striped table-bordered no-footer">
                <thead>
                <tr>
                    <th>Service</th>
                    <th>Monthly Cost</th>
                    <th>Sign Up</th>
                </tr>
                </thead>

                <tbody>
                    @forelse($monitor as $m)
                        <tr>
                            <td>{{ $m->name }}</td>
                            @if ($m->base_cost == 0)
                                <td><strike>$0</strike> <strong>FREE!</strong> </td>
                            @else
                                <td>$ {{ number_format($m->base_cost, 0) }}</td>
                            @endif
                            <td><button class="btn btn-success" onclick="window.location = '{{ route('subscriptions.create', $m->id) }}'">Subscribe!</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No FBO Monitor Services found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection