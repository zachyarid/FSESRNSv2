@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">By Pilot</h4>
                <table id="by_pilot" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                        <tr>
                            <th>Pilot</th>
                            <th>Pilot Profit</th>
                            <th>Group Revenue</th>
                            <th>Fuel Cost</th>
                            <th>Group Profit</th>
                            <th>Total Flights</th>
                            <th>Total Distance</th>
                            <th>Profit per nm</th>
                            <th>Profit per flight</th>
                            <th>Total Time Flown</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @if (isset($bypilot))
                            @if (count($bypilot) > 0)
                                @foreach ($bypilot as $r)
                                    <tr>
                                        <td>{{ $r->pilot }}</td>
                                        <td>${{ number_format($r->PilotProfit) }}</td>
                                        <td>${{ number_format($r->GroupProfit) }}</td>
                                        <td>${{ number_format($r->fuelcost, 2) }}</td>
                                        <td>${{ number_format($r->GroupProfit - $r->fuelcost, 2) }}</td>
                                        <td>{{ $r->TotalFlights }}</td>
                                        <td>{{ number_format($r->TotalDistance, 0) }} nm</td>
                                        <td>${{ number_format(($r->GroupProfit - $r->fuelcost)/$r->TotalDistance, 2) }}</td>
                                        <td>${{ number_format(($r->GroupProfit - $r->fuelcost)/$r->TotalFlights, 2) }}</td>
                                        <td>{{ $r->totalflighttime }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="10">No data found for this month!</td></tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">By Aircraft</h4>
                <table id="by_aircraft" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Aircraft</th>
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
                        @forelse ($reportData->aircraft->income as $k => $v)
                            <tr>
                                <td>{{ $k }}</td>
                                <td>${{ $v->sum() - $reportData->aircraft->pilot_fee[$k]->sum() }}</td>
                                <td>$</td>
                                <td>$</td>
                                <td>$</td>
                                <td>{{ $reportData->aircraft->distance[$k]->sum() }} nm</td>
                                <td>$</td>
                                <td></td>
                            </tr>
                        @empty
                        <tr><td colspan="9">No data found for this month!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">GCFs Paid By FBO</h4>
                <table id="gcf_fbo" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>FBO</th>
                        <th>Total</th>
                        <th>Total GCFs</th>
                        <th>Average GCF</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($gcfpdfbo))
                        @if (count($gcfpdfbo) > 0)
                            @foreach ($gcfpdfbo as $g)
                                <tr>
                                    <td>{{ $g->fbo }}</td>
                                    <td>${{ number_format($g->Total, 2) }}</td>
                                    <td>{{ number_format($g->GCFs, 0) }}</td>
                                    <td>${{ number_format($g->AverageGCF,2) }}</td>
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

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">GCFs Paid By Payer</h4>
                <table id="gcf_payer" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Paid To</th>
                        <th>Total</th>
                        <th>Total GCFs</th>
                        <th>Average GCF</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($gcfpdpay))
                        @if (count($gcfpdpay) > 0)
                            @foreach ($gcfpdpay as $p)
                                <tr>
                                    <td>{{ $p->p_to }}</td>
                                    <td>${{ number_format($p->Total, 2) }}</td>
                                    <td>{{ number_format($p->GCFs, 0) }}</td>
                                    <td>${{ number_format($p->AverageGCF,2) }}</td>
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

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">Fuel Payments By Payer</h4>
                <div class="col-lg-6">
                    <p class="text-sm-center"><strong>Total 100LL Fuel Payments:</strong> ${{ number_format($all100ll, 2) }}</p>
                </div>
                <div class="col-lg-6">
                    <p class="text-sm-center"><strong>Total JetA Fuel Payments:</strong> ${{ number_format($alljeta, 2) }}</p>
                </div>
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
                                    <td>{{ $f->paidto }}</td>
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
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive card-box">
                <h4 class="page-title text-lg-center">Flight Legs by Profitability</h4>
                <p class="text-sm-center">* does not include fuel/maintenance expenditures</p>
                <table id="leg_profit" class="table table-striped table-bordered dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Leg</th>
                        <th>Total Income</th>
                        <th>Frequency Leg is Flown</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($flbyprof))
                        @if (count($flbyprof) > 0)
                            @foreach ($flbyprof as $f)
                                <tr>
                                    <td>{{ $f->route }}</td>
                                    <td>${{ number_format($f->inc, 2) }}</td>
                                    <td>{{ number_format($f->ct, 0) }}</td>
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
@endsection

@section('script-source')
    <script>
        $(document).ready(function () {
            $('#by_pilot').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#by_aircraft').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#gcf_fbo').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#gcf_payer').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#fuel_payer').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });

            $('#leg_profit').DataTable({
                responsive: true,
                bLengthChange: true,
                aaSorting: [],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page'
                }
            });
        });

        $( "table thead th" ).addClass( "text-sm-center" );
    </script>
@endsection