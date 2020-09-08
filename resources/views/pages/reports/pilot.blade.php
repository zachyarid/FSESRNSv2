@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-two">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#AircraftModal">View</button>
                <h4 class="text-muted text-uppercase m-b-15">Aircraft</h4>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box tilebox-two">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#FuelModal">View</button>
                <h5 class="text-muted text-uppercase m-b-15">Fuel Payments</h5>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xs-12 col-lg-12 col-xl-8">
            <div class="card-box">
                <h4 class="text-muted text-uppercase header-title">
                    Historic Group Profit
                    <span class="text-muted m-t-0 m-b-20" style="font-size:12px;">* does not include Maintenance or Ownership costs</span></h4>
                <div id="historic-gp" style="height: 250px;"></div>
            </div>
        </div><!-- end col-->
    </div>

    <div id="AircraftModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">By Aircraft</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="aircraft" class="table table-striped table-bordered dataTable no-footer">
                            <thead>
                            <tr>
                                <th>Aircraft</th>
                                <th>Type</th>
                                <th>Group Revenue</th>
                                <th>Fuel Cost</th>
                                <th>Group Profit</th>
                                <th>Pilot Profit</th>
                                <th>Total Distance</th>
                                <th>Profit per nm</th>
                                <th>Total Time Flown</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($aircraft))
                                @if (count($aircraft) > 0)
                                    @foreach ($aircraft as $a)
                                        <tr>
                                            <td>{{ $a->aircraft }}</td>
                                            <td>{{ $a->make_model }}</td>
                                            <td>${{ number_format($a->GroupProfit) }}</td>
                                            <td>${{ number_format($a->fuelcost, 2) }}</td>
                                            <td>${{ number_format($a->GroupProfit - $a->fuelcost, 2) }}</td>
                                            <td>${{ number_format($a->PilotProfit, 0) }}</td>
                                            <td>{{ number_format($a->TotalDistance, 0) }} nm</td>
                                            <td>${{ number_format(($a->GroupProfit - $a->fuelcost)/$a->TotalDistance, 2) }}</td>
                                            <td>{{ $a->totalflighttime }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="9">No data found for this month!</td></tr>
                                @endif
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="FuelModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">Fuel Payment Data</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs m-b-10" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-expanded="true">All Fuel Payments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ll-tab" data-toggle="tab" href="#ll" role="tab" aria-controls="ll" aria-expanded="false">100LL Payments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="jeta-tab" data-toggle="tab" href="#jeta" role="tab" aria-controls="jeta" aria-expanded="false">JetA Payments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="acfbofuel-tab" data-toggle="tab" href="#acfbofuel" role="tab" aria-controls="acfbofuel" aria-expanded="false">Payments by FBO for Aircraft</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div role="tabpanel" class="tab-pane fade active in" id="all">
                            <div class="table-responsive">
                                <table id="fuel_payer" class="table table-striped table-bordered dataTable no-footer">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($fuelpmtpay))
                                        @if (count($fuelpmtpay) > 0)
                                            @foreach ($fuelpmtpay as $f)
                                                <tr>
                                                    <td>{!! $f->paidto !!}</td>
                                                    <td>${{ number_format($f->total, 2) }}</td>
                                                    <td>{{ number_format($f->count, 0) }}</td>
                                                    <td>${{ number_format($f->avgpmt, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="4">No data found for this month!</td></tr>
                                        @endif
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ll">
                            <div class="table-responsive">
                                <table id="ll_payments" class="table table-striped table-bordered dataTable no-footer">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($ll))
                                        @if (count($ll) > 0)
                                            @foreach ($ll as $f)
                                                <tr>
                                                    <td>{!! $f->paidto !!}</td>
                                                    <td>${{ number_format($f->total, 2) }}</td>
                                                    <td>{{ number_format($f->count, 0) }}</td>
                                                    <td>${{ number_format($f->avgpmt, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="4">No data found for this month!</td></tr>
                                        @endif
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="jeta">
                            <div class="table-responsive">
                                <table id="jeta_payments" class="table table-striped table-bordered dataTable no-footer">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($jeta))
                                        @if (count($jeta) > 0)
                                            @foreach ($jeta as $f)
                                                <tr>
                                                    <td>{!! $f->paidto !!}</td>
                                                    <td>${{ number_format($f->total, 2) }}</td>
                                                    <td>{{ number_format($f->count, 0) }}</td>
                                                    <td>${{ number_format($f->avgpmt, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="4">No data found for this month!</td></tr>
                                        @endif
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="acfbofuel">
                            <div class="table-responsive">
                                <table id="acfbofuel_payments" class="table table-striped table-bordered dataTable no-footer">
                                    <thead>
                                    <tr>
                                        <th>Aircraft</th>
                                        <th>FBO</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($acfbofuel['message']))
                                        @if (count($acfbofuel['message']) > 0)
                                            @foreach ($acfbofuel['message'] as $f)
                                                <tr>
                                                    <td style="width:75px;">{!! $f->aircraft !!}</td>
                                                    <td>{{ $f->fbo }}</td>
                                                    <td>${{ number_format($f->Total, 2) }}</td>
                                                    <td>{{ number_format($f->count, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="4">No data found for this month!</td></tr>
                                        @endif
                                    @endif
                                    </tbody>
                                </table>
                            </div>
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
            $('#aircraft, #fuel_payer, #ll_payments, #jeta_payments').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#acfbofuel_payments').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                columnDefs: [
                    { width: "75px", targets: 0 }
                ],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2,
            });

            Morris.Bar({
                "barSizeRatio": 0.75,
                "barGap": 3,
                "barOpacity": 1,
                "barRadius": [0, 0, 0, 0],
                "xLabelMargin": 50,
                "barColors": ["#0b62a4", "#7a92a3", "#4da74d", "#afd8f8", "#edc240", "#cb4b4b", "#9440ed"],
                "stacked": false,
                "xkey": ["y"],
                "ykeys": ["a", "b"],
                "labels": ["Group Profit", "Pilot Profit"],
                "ymax": "auto",
                "onlyIntegers": true,
                "ymin": 0,
                "hideHover": "auto",
                "axes": true,
                "grid": true,
                "gridTextColor": "#888",
                "gridTextSize": "12",
                "gridTextFamily": "sans-serif",
                "gridTextWeight": "normal",
                "resize": false,
                "rangeSelectColor": "#eef",
                "padding": 25,
                "numLines": 5,
                "eventStrokeWidth": 1,
                "eventLineColors": ["#005a04", "#ccffbb", "#3a5f0b", "#005502"],
                "goalStrokeWidth": 1,
                "goalLineColors": ["#666633", "#999966", "#cc6666", "#663333"],
                "parseTime": true,
                "xLabelAngle": 30,
                "element": "historic-gp",
                "data": [ {!! $historic !!} ],
                "functions": ["hoverCallback", "formatter", "dateFormat"],
                "yLabelFormat": function (x) {
                    return formatter.format(x);
                },
            });
        });
    </script>
@endsection