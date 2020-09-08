@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box tilebox-two">
                <div class="form-inline">
                    <input type="hidden" id="threshold_id" value="{{ $threshold->id }}">
                    <div class="form-group">
                        <label for="supply_threshold">Supply Threshold (days)</label>
                        <input type="number" class="form-control m-l-5" id="supply_threshold" value="{{ $threshold->supply_threshold }}">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="changeThresholds()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box tilebox-two">
                <h5>Most recent alerts sent</h5>
                <div class="table-responsive">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box tilebox-two">
                <div class="table-responsive">
                    <table id="fbos" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                        <thead>
                        <tr>
                            <th>FBO</th>
                            <th>Supplies Remaining</th>
                            <th>Supply Consumption</th>
                            <th>Supplies Remaining</th>
                            <th>Date to Close</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($fbos as $fbo)
                            <tr>
                                <td>{{ $fbo->icao }} - {{ $fbo->name }}</td>
                                <td>{{ $fbo->supplies }} kgs</td>
                                <td>{{ $fbo->supplies_per_day }} kgs / day</td>

                                @if ($fbo->supplied_days < $threshold->supply_threshold)
                                    <td style="color:red;">{{ $fbo->supplied_days }} days</td>
                                @else
                                    <td>{{ $fbo->supplied_days }} days</td>
                                @endif

                                <td>
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $now->addDays($fbo->supplied_days);
                                        echo $now->format('M d. Y')
                                    @endphp
                                </td>

                                <td><button class="btn btn-info" data-toggle="modal" data-target="#buy_supplies">Buy Supplies</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No data available!</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="buy_supplies" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title text-lg-center" id="myModalLabel">Resupply FBO</h3>
                    <h4 class="modal-title text-lg-center" id="fboTitle"></h4>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group">
                            <input class="form-control" id="supplies_to_buy" type="text" placeholder="How many kgs of supplies to buy?" />
                            <p class="text-muted text-center" style="color:red;">* Completing this form will purchase supplies for your FBO from the system at $6.75 per kilogram. Please ensure this is what you want to do before clicking the button!</p>
                        </div>

                        <div class="form-group text-center">
                            <button class="btn btn-info">Buy Supplies</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();

            $('#fbos').DataTable({
                responsive: true,
                bLengthChange: true,
                order: [[ 3, "desc" ], [ 0, 'desc' ]],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });
        });

        function changeThresholds()
        {
            var supply = $('#supply_threshold').val();
            var id = $('#threshold_id').val();

            $.ajax({
                type: 'POST',
                url: '{{ route('monitor.changest') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    supply: supply,
                },
                success: function (data) {
                    if (data.success)
                    {
                        toastr["success"](data.message,
                            "Success!", {
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": false,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "500",
                                "timeOut": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    </script>
@endsection