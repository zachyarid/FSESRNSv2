@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-12">
            <div class="card-box tilebox-two">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Solo Flying Profit</th>
                            <th>Group Flying Pilot Fees</th>
                            <th>Distance</th>
                            <th>Total Time</th>
                            <th>Number of Flights</th>
                            <th>Total Profit per nm</th>
                            <th>Total Profit per hour</th>
                            <th>Total Profit per flight</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>${{ number_format($reportData->summaryRow['solo_flying'],0) }}</td>
                            <td>${{ number_format($reportData->summaryRow['pilot_fee'],0) }}</td>
                            <td>{{ number_format($reportData->summaryRow['distance'],0) }} nm</td>
                            <td>{{ number_format($reportData->summaryRow['flight_time'],2) }}</td>
                            <td>{{ $reportData->summaryRow['count'] }}</td>
                            <td>${{ number_format($reportData->summaryRow['prof_per_nm'],2) }}</td>
                            <td>${{ number_format($reportData->summaryRow['prof_per_hr'],2) }}</td>
                            <td>${{ number_format($reportData->summaryRow['prof_per_flt'],2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card-box tilebox-two">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#byAircraftModal">View</button>
                <h4 class="text-muted text-uppercase m-b-15">By Aircraft</h4>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card-box tilebox-two">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#byGCFFBOModal">View</button>
                <h4 class="text-muted text-uppercase m-b-15">GCFs By FBO</h4>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card-box tilebox-two">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#byGCFPayerModal">View</button>
                <h4 class="text-muted text-uppercase m-b-15">GCFs By Payer</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-12 col-xl-8">
            <div class="card-box">
                <h4 class="text-muted text-uppercase header-title m-t-0 m-b-20">Historic Pilot Profit</h4>
                <div id="historic-gp" style="height: 250px;"></div>
            </div>
        </div><!-- end col-->

        <div class="col-xs-12 col-lg-12 col-xl-4">
            <div class="card-box" style="height:325px;">
                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#byFuelPayerModal">View</button>
                <h4 class="text-muted text-uppercase header-title m-t-0 m-b-30">Fuel Payments By Payer</h4>

                @php
                    $jetatotal = 0;
                    foreach ($reportData->fuelJetA as $key => $val)
                    {
                        $jetatotal += $val['amount'];
                    }

                    $lltotal = 0;
                    foreach ($reportData->fuel100LL as $key => $val)
                    {
                        $lltotal += $val['amount'];
                    }
                @endphp
                <p><strong class="text-muted">Total 100LL:</strong> ${{ number_format($lltotal, 2) }}</p>
                <p><strong class="text-muted">Total JetA:</strong> ${{ number_format($jetatotal, 2) }}</p>
                <p><strong class="text-muted">Total:</strong> ${{ number_format($jetatotal+$lltotal, 2) }}</p>

                <hr />

                <button class="btn btn-sm btn-custom waves-effect waves-light pull-xs-right" data-toggle="modal" data-target="#byFLProfModal">View</button>
                <h4 class="text-muted text-uppercase header-title m-t-0 m-b-30">Flight Legs By Profitability</h4>
            </div>
        </div><!-- end col-->
    </div>

    <div id="byAircraftModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">By Aircraft</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="by_aircraft" class="table table-striped table-bordered dataTable no-footer width" style="width:100%">
                            <thead>
                            <tr>
                                <th>Aircraft</th>
                                <th>Type</th>
                                <th>Pilot Profit</th>
                                <th>Total Distance</th>
                                <th>Number of Flights</th>
                                <th>Profit per nm</th>
                                <th>Profit per hr</th>
                                <th>Total Time Flown</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($reportData->flightsByAircraft as $k => $v)
                                <tr>
                                    @if (empty($v['group_name']))
                                        <td>{{ $k }}</td>
                                        <td>{{ $v['make_model'] }}</td>
                                        <td>${{ number_format($v['income']-$v['crew_cost']-$v['booking_fee']+$v['bonus']-$v['gcf']-$v['rental_cost'],0) }}</td>
                                        <td>{{ number_format($v['distance'],0) }} nm</td>
                                        <td>{{ number_format($v['count'],0) }}</td>
                                        <td>${{ number_format(($v['income']-$v['crew_cost']-$v['booking_fee']+$v['bonus']-$v['gcf']-$v['rental_cost'])/$v['distance'],2) }}</td>
                                        <td>${{ number_format(($v['income']-$v['crew_cost']-$v['booking_fee']+$v['bonus']-$v['gcf']-$v['rental_cost'])/$v['flight_time'],2) }}</td>
                                        <td>{{ number_format($v['flight_time'],1) }}</td>
                                    @elseif (!empty($v['group_name']))
                                        <td>{{ $k }} <span class="text-muted" style="font-size:11px">({{ $v['group_name'] }})</span></td>
                                        <td>{{ $v['make_model'] }}</td>
                                        <td>${{ number_format($v['pilot_fee'],0) }}</td>
                                        <td>{{ number_format($v['distance'],0) }} nm</td>
                                        <td>{{ number_format($v['count'],0) }}</td>
                                        <td>${{ number_format($v['pilot_fee']/$v['distance'],2) }}</td>
                                        <td>${{ number_format($v['pilot_fee']/$v['flight_time'],2) }}</td>
                                        <td>{{ number_format($v['flight_time'],1) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="8">No data found for this month!</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="byGCFFBOModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog" style="width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">GCFs Paid by FBO</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="gcf_fbo" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                            <thead>
                            <tr>
                                <th>FBO</th>
                                <th>Total</th>
                                <th>Total GCFs</th>
                                <th>Average GCF</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($reportData->gcfsByFBO as $k => $v)
                                <tr>
                                    <td>{!! $k !!}</td>
                                    <td>${{ number_format($v['amount'], 2) }}</td>
                                    <td>{{ number_format($v['count'], 0) }}</td>
                                    <td>${{ number_format($v['amount']/$v['count'],2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No data available!</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="byGCFPayerModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">GCFs Paid By Payer</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="gcf_payer" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                            <thead>
                            <tr>
                                <th>Paid To</th>
                                <th>Total</th>
                                <th>Total GCFs</th>
                                <th>Average GCF</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($reportData->gcfsByPayer as $k => $v)
                                <tr>
                                    <td>{!! $k !!}</td>
                                    <td>${{ number_format($v['amount'], 2) }}</td>
                                    <td>{{ number_format($v['count'], 0) }}</td>
                                    <td>${{ number_format($v['amount']/$v['count'],2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No data available!</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="byFLProfModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">Flight Legs By Profitability</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <p class="text-sm-center">* does not include fuel/maintenance expenditures</p>
                        <table id="leg_profit" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:300px;">Leg</th>
                                <th>Total Income</th>
                                <th>Frequency Leg is Flown</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($reportData->flightsByProfitability as $k => $v)
                                <tr>
                                    <td>{!! $k !!} {{ isset($v['group_name']) ? $v['group_name'] : '' }}</td>
                                    <td>${{ number_format($v['pilot_fee'], 2) }}</td>
                                    <td>{{ number_format($v['count'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No data available!</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="byFuelPayerModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg" style="width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-lg-center" id="myModalLabel">Fuel Payments By Payer</h4>
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
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div role="tabpanel" class="tab-pane fade active in" id="all">
                            <div class="table-responsive">
                                <table id="fuel_payer" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($reportData->fuelByPayer as $k => $v)
                                        <tr>
                                            <td>{!! $k !!}</td>
                                            <td>${{ number_format($v->sum(), 2) }}</td>
                                            <td>{{ number_format($v->count(), 0) }}</td>
                                            <td>${{ number_format($v->average(), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No data available!</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ll">
                            <div class="table-responsive">
                                <table id="ll_payments" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($reportData->fuel100LL as $k => $v)
                                        <tr>
                                            <td>{!! $k !!}</td>
                                            <td>${{ number_format($v['amount'], 2) }}</td>
                                            <td>{{ number_format($v['count'], 0) }}</td>
                                            <td>${{ number_format($v['amount']/$v['count'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No data available!</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="jeta">
                            <div class="table-responsive">
                                <table id="jeta_payments" class="table table-striped table-bordered dataTable no-footer" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Paid To</th>
                                        <th>Total</th>
                                        <th>Total Payments</th>
                                        <th>Average Fuel Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($reportData->fuelJetA as $k => $v)
                                        <tr>
                                            <td>{!! $k !!}</td>
                                            <td>${{ number_format($v['amount'], 2) }}</td>
                                            <td>{{ number_format($v['count'], 0) }}</td>
                                            <td>${{ number_format($v['amount']/$v['count'], 2) }}</td>
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
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#by_aircraft').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [2],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#gcf_fbo').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [1],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#gcf_payer').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [1],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#leg_profit').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [1],
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
                "ykeys": ["a"],
                "labels": ["Pilot Profit"],
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