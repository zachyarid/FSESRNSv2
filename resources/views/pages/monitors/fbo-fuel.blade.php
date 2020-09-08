@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box tilebox-two">
                <div class="form-inline">
                    <input type="hidden" id="threshold_id" value="{{ $threshold->id }}">
                    <div class="form-group">
                        <label for="ll_threshold">100LL Threshold (gallons)</label>
                        <input type="number" class="form-control m-l-5" id="ll_threshold" value="{{ $threshold->ll_threshold }}">
                    </div>
                    <div class="form-group m-l-5">
                        <label for="jeta_threshold">JetA Threshold (gallons)</label>
                        <input type="number" class="form-control m-l-5" id="jeta_threshold" value="{{ $threshold->jeta_threshold }}">
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
                            <th>100LL Fuel</th>
                            <th>JetA Fuel</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($fbos as $fbo)
                            <tr>
                                <td>{{ $fbo->icao }} - {{ $fbo->name }}</td>
                                @if ($fbo->fuel_100ll * config('fse.kgToGal') < $threshold->ll_threshold)
                                    <td style="color:red;">{{ number_format($fbo->fuel_100ll * config('fse.kgToGal'),0) }} gallons</td>
                                @else
                                    <td>{{ number_format($fbo->fuel_100ll * config('fse.kgToGal'),0) }} gallons</td>
                                @endif

                                @if ($fbo->fuel_jeta * config('fse.kgToGal') < $threshold->jeta_threshold)
                                    <td style="color:red;">{{ number_format($fbo->fuel_jeta * config('fse.kgToGal'),0) }} gallons</td>
                                @else
                                    <td>{{ number_format($fbo->fuel_jeta * config('fse.kgToGal'),0) }} gallons</td>
                                @endif

                                <td><a class="btn btn-info" href="{{ config('fse.gameWorldURL') . config('fse.buyWholesaleFuelURL') . $fbo->id }}" target="_blank">Buy Fuel</a></td>
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

    <div id="change_minimums" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title text-lg-center" id="myModalLabel">FBO Profit and Loss</h3>
                    <h4 class="modal-title text-lg-center" id="fboTitle"></h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tblPandl" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                            <thead>
                            <tr>
                                <th>Month Year</th>
                                <th>GCF Revenue</th>
                                <th>Refuelling Profit</th>
                                <th>Maintenance Profit</th>
                                <th>Equipment Install Profit</th>
                                <th>PT Income</th>
                                <th>Supply Cost</th>
                                <th>Net Profit/Loss</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
                order: [[ 2, "asc" ], [ 1, 'asc' ]],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });
        });

        function changeThresholds()
        {
            var ll = $('#ll_threshold').val();
            var jeta = $('#jeta_threshold').val();
            var id = $('#threshold_id').val();

            $.ajax({
                type: 'POST',
                url: '{{ route('monitor.changeft') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    jeta: jeta,
                    ll: ll
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