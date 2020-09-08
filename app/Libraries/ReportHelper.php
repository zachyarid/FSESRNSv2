<?php
/**
 * Created by PhpStorm.
 * User: zachyarid
 * Date: 6/6/18
 * Time: 8:10 PM
 */

namespace App\Libraries;

use App\FSEFbo;
use App\FSEFlightLog;
use App\FSEPayment;
use App\Group;
use App\SerializedMonthlyReport;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportHelper
{
    protected $group_id;
    protected $subscription;
    protected $group;

    public function __construct($sid)
    {
        $this->subscription = Subscription::find($sid);
        $this->group = Group::find($this->subscription->group->id);
        $this->group_id = $this->group->id;
    }

    public function getFBOReportData()
    {
        $reportMonthYear = Carbon::parse(session()->get('monthyear'));
        $lastDayOfMonth = Carbon::now()->endOfMonth();
        $diffInDays = $lastDayOfMonth->diffInDays($reportMonthYear);

        // Is there a serialized monthly report for this subscription and monthyear yet?
        $serializedReport = SerializedMonthlyReport::where([
            'subscription_id' => $this->subscription->id,
            'monthyear' => session()->get('monthyear'),
            'type' => 'fbo'
        ])->first();

        if ($serializedReport)
        {
            return unserialize($serializedReport->content);
        }

        $payments = FSEPayment::month(session()->get('monthyear'))
            ->group($this->group_id)
            ->get();

        $fbos = FSEFbo::limitgroup($this->group_id)
            ->get();

        // Totals
        $paymentsByPayee = [];
        $maintrev = 0;
        $maintcost = 0;

        // Loop for gcfs by fbo/payee
        foreach ($payments as $payment)
        {
            if (Str::contains($payment->p_to, $this->group->name))
            {
                if (Str::contains($payment->reason, config('fse.stringFboGCF')))
                {
                    // Payee
                    @$paymentsByPayee[$payment->p_from]['gcfs'] += $payment->amount;
                    @$paymentsByPayee[$payment->p_from]['count']++;

                    // By FBO
                    foreach ($fbos as $fbo)
                    {
                        $fbostring = $fbo->icao . ' ' . $fbo->name;
                        if ($fbostring == $payment->fbo)
                        {
                            $fbo->gcfrev += $payment->amount;
                            $fbo->gcfcount++;
                        }
                    }
                }

                else if (Str::contains($payment->reason, config('fse.stringAircraftMaintenance')))
                {
                    $maintrev += $payment->amount;
                }

                else if (Str::contains($payment->reason, 'Refuelling'))
                {
                    $rev = $payment->amount;
                    $comment = $payment->comment;
                    $gallonsSold = $this->findGallons($comment);

                    foreach ($fbos as $fbo)
                    {
                        $fbostring = $fbo->icao . ' ' . $fbo->name;
                        if ($fbostring == $payment->fbo)
                        {
                            if (Str::contains($payment->reason, 'JetA'))
                            {
                                $cogs = $gallonsSold * config('fse.jetACostPerGallon');
                                $profit = $rev - $cogs;

                                $fbo->jetaprof += $profit;
                                $fbo->jetarev += $rev;
                                $fbo->jetagallonssold += $gallonsSold;
                                $fbo->jetacog += $cogs;
                            }

                            else if (Str::contains($payment->reason, '100LL'))
                            {
                                $cogs = $gallonsSold * config('fse.llCostPerGallon');
                                $profit = $rev - $cogs;

                                $fbo->llprof += $profit;
                                $fbo->llrev += $rev;
                                $fbo->llgallonssold += $gallonsSold;
                                $fbo->llcog += $cogs;
                            }
                        }
                    }
                }
            }

            else if (Str::contains($payment->p_from, $this->group->name))
            {
                if (Str::contains($payment->reason, 'Cost') && Str::contains($payment->reason, 'maintenance'))
                {
                    $maintcost += $payment->amount;
                }
            }
        }

        // Average calculations
        foreach ($fbos as $fbo)
        {
            @$gcfavg = $fbo->gcfrev/$fbo->gcfcount;
            $fbo->gcfavg = number_format($gcfavg,2);
            $fbo->gcfrev = number_format($fbo->gcfrev,2);

        }
        foreach ($paymentsByPayee as $key => $value)
        {
            $paymentsByPayee[$key]['avg'] = $value['gcfs']/$value['count'];
        }

        // Totals
        $totalrev = 0;
        $totalgal = 0;
        $totalcogs = 0;
        $totalprof = 0;
        foreach ($fbos as $fbo)
        {
            $totalrev += $fbo->jetarev + $fbo->llrev;
            $totalgal += $fbo->jetagallonssold + $fbo->llgallonssold;
            $totalcogs += $fbo->jetacog + $fbo->llcog;
            $totalprof += $fbo->jetaprof + $fbo->llprof;
        }

        // Package up the result
        $result = new \stdClass();
        $result->fbos = $fbos;
        $result->paymentsByPayee = $paymentsByPayee;
        $result->maintrev = $maintrev;
        $result->maintcost = $maintcost;
        $result->totalrev = $totalrev;
        $result->totalgal = $totalgal;
        $result->totalcogs = $totalcogs;
        $result->totalprof = $totalprof;

        if ($diffInDays > 31)
        {
            // make new report serliazation
            SerializedMonthlyReport::updateOrCreate(
                ['subscription_id' => $this->subscription->id,
                    'group_id' => $this->group_id,
                    'monthyear' => session()->get('monthyear'),
                    'content' => serialize($result),
                    'type' => 'fbo'],
                ['subscription_id' => $this->subscription->id,
                'group_id' => $this->group_id,
                'monthyear' => session()->get('monthyear'),
                'content' => serialize($result),
                'type' => 'fbo']);
        }

        return $result;
    }

    public function getFlightReportData()
    {
        $reportMonthYear = Carbon::parse(session()->get('monthyear'));
        $lastDayOfMonth = Carbon::now()->endOfMonth();
        $diffInDays = $lastDayOfMonth->diffInDays($reportMonthYear);

        // Is there a serialized monthly report for this subscription and monthyear yet?
        $serializedReport = SerializedMonthlyReport::where([
            'subscription_id' => $this->subscription->id,
            'monthyear' => session()->get('monthyear'),
            'type' => 'fbo'
        ])->first();

        if ($serializedReport)
        {
            return unserialize($serializedReport->content);
        }

        $flightlogs = FSEFlightLog::month(session()->get('monthyear'))
            ->group($this->group_id)
            ->with('pilotid') # need the fseid for the pilot for fuel payments
            ->where('type', '=', 'flight')
            ->get();

        // Fuelling
        $refuellingPayments = FSEPayment::month(session()->get('monthyear'))
            ->likereason('Refuelling')
            ->group($this->group_id)
            ->get();

        $fuelByPayer = $refuellingPayments->mapToGroups(function ($item) {
            return [$item['p_to'] => $item['amount']];
        });

        $jeta = [];
        $ll = [];

        foreach ($refuellingPayments as $payment)
        {
            if (Str::contains($payment->reason, 'Refuelling'))
            {
                $rev = $payment->amount;
                $comment = $payment->comment;
                $gallonsSold = $this->findGallons($comment);

                if (Str::contains($payment->reason, 'JetA'))
                {
                    $cogs = $gallonsSold * config('fse.jetACostPerGallon');
                    $profit = $rev - $cogs;

                    @$jeta[$payment->p_to]['count']++;
                    @$jeta[$payment->p_to]['amount'] += $payment->amount;

                }

                else if (Str::contains($payment->reason, '100LL'))
                {
                    $cogs = $gallonsSold * config('fse.llCostPerGallon');
                    $profit = $rev - $cogs;

                    @$ll[$payment->p_to]['count']++;
                    @$ll[$payment->p_to]['amount'] += $payment->amount;
                }
            }
        }

        $groupedFuelByPilot = $refuellingPayments->mapToGroups(function ($item) {
            $comment = $item['comment'];
            $commentExplode = explode(':', $comment);
            $userID = explode(" ", $commentExplode[1]);
            return [$userID[1] => $item['amount']];
        });

        $groupedFuelByAircraft = $refuellingPayments->mapToGroups(function ($item) {
            return [$item['aircraft'] => $item['amount']];
        });

        // GCF Payments
        $gcfPayments = FSEPayment::month(session()->get('monthyear'))
            ->equalreason(config('fse.stringFboGCF'))
            ->group($this->group_id)
            ->get();

        $groupedGCFByFBO = $gcfPayments->mapToGroups(function ($item) {
            return [$item['fbo'] => $item['amount']];
        });

        $groupedGCFByPayer = $gcfPayments->mapToGroups(function ($item) {
            return [$item['p_to'] => $item['amount']];
        });

        //Totals
        $flightsByPilot = [];
        $flightsByAircraft = [];
        $flightsByProfitability = [];

        foreach ($flightlogs as $flight)
        {
            @$flightsByPilot[$flight->pilot]['count']++;
            @$flightsByPilot[$flight->pilot]['income'] += $flight->income;
            @$flightsByPilot[$flight->pilot]['pilot_fee'] += $flight->pilot_fee;
            @$flightsByPilot[$flight->pilot]['crew_cost'] += $flight->crew_cost;
            @$flightsByPilot[$flight->pilot]['booking_fee'] += $flight->booking_fee;
            @$flightsByPilot[$flight->pilot]['bonus'] += $flight->bonus;
            @$flightsByPilot[$flight->pilot]['fuel_cost'] += $flight->fuel_cost;
            @$flightsByPilot[$flight->pilot]['gcf'] += $flight->gcf;
            @$flightsByPilot[$flight->pilot]['distance'] += $flight->distance;
            @$flightsByPilot[$flight->pilot]['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
            @$flightsByPilot[$flight->pilot]['rental_units'] += $this->convertTimeToDecimal($flight->rental_units);
            @$flightsByPilot[$flight->pilot]['rental_cost'] += $flight->rental_cost;
            @$flightsByPilot[$flight->pilot]['fseid'] = $flight->pilotid->id;
            @$flightsByPilot[$flight->pilot]['fuel_payments'] = 0;

            @$flightsByAircraft[$flight->aircraft]['count']++;
            @$flightsByAircraft[$flight->aircraft]['income'] += $flight->income;
            @$flightsByAircraft[$flight->aircraft]['pilot_fee'] += $flight->pilot_fee;
            @$flightsByAircraft[$flight->aircraft]['crew_cost'] += $flight->crew_cost;
            @$flightsByAircraft[$flight->aircraft]['booking_fee'] += $flight->booking_fee;
            @$flightsByAircraft[$flight->aircraft]['bonus'] += $flight->bonus;
            @$flightsByAircraft[$flight->aircraft]['fuel_cost'] += $flight->fuel_cost;
            @$flightsByAircraft[$flight->aircraft]['gcf'] += $flight->gcf;
            @$flightsByAircraft[$flight->aircraft]['distance'] += $flight->distance;
            @$flightsByAircraft[$flight->aircraft]['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
            @$flightsByAircraft[$flight->aircraft]['rental_units'] += $this->convertTimeToDecimal($flight->rental_units);
            @$flightsByAircraft[$flight->aircraft]['rental_cost'] += $flight->rental_cost;
            @$flightsByAircraft[$flight->aircraft]['make_model'] = $flight->make_model;
            @$flightsByAircraft[$flight->aircraft]['fuel_payments'] = 0;

            @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['count']++;
            @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['income'] += $flight->income-$flight->pilot_fee-$flight->crew_cost-$flight->booking_fee+$flight->bonus-$flight->fuel_cost-$flight->gcf-$flight->rental_cost;
        }

        foreach ($flightsByPilot as $key1 => $value1)
        {
            foreach ($groupedFuelByPilot as $key2 => $value2)
            {
                if ($value1['fseid'] == $key2)
                {
                    $flightsByPilot[$key1]['fuel_payments'] = $value2->sum();
                }
            }
        }

        foreach ($flightsByAircraft as $ac1 => $value)
        {
            foreach ($groupedFuelByAircraft as $ac2 => $fuel)
            {
                if ($ac1 == $ac2)
                {
                    $flightsByAircraft[$ac1]['fuel_payments'] = $fuel->sum();
                }
            }
        }

        $result = new \stdClass();
        $result->flightsByPilot = $flightsByPilot;
        $result->flightsByAircraft = $flightsByAircraft;
        $result->gcfsByFBO = $groupedGCFByFBO;
        $result->gcfsByPayer = $groupedGCFByPayer;
        $result->fuelByPayer = $fuelByPayer;
        $result->flightsByProfitablilty = $flightsByProfitability;
        $result->fuelJetA = $jeta;
        $result->fuel100LL = $ll;

        if ($diffInDays > 31)
        {
            // make new report serliazation
            SerializedMonthlyReport::updateOrCreate(
                ['subscription_id' => $this->subscription->id,
                    'group_id' => $this->group_id,
                    'monthyear' => session()->get('monthyear'),
                    'content' => serialize($result),
                    'type' => 'flight'],
                ['subscription_id' => $this->subscription->id,
                    'group_id' => $this->group_id,
                    'monthyear' => session()->get('monthyear'),
                    'content' => serialize($result),
                    'type' => 'flight']);
        }

        return $result;
    }

    public function getFlightsByProf() {
        return DB::table('fse_flightlogs')
            ->selectRaw('concat(f_from," -> ", f_to) as route, sum(income-pilot_fee-crew_cost-booking_fee+bonus-fuel_cost-gcf-rental_cost) as inc, count(*) as ct')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where('group_id', $this->group_id)
            ->groupBy('route')

            ->orderByDesc('inc')
            ->limit(25)
            ->get();
    }

    public function getHistoricGCFs()
    {
        $other = [];

        $group = Group::find($this->group_id);

        /*$payments = FSEPayment::lastsixmonths()
            ->group($this->group_id)
            ->paidto($group->name)
            ->equalreason(config('fse.stringFboGCF'))->get();

        $historic = [];
        foreach ($payments as $pay)
        {
            $monthyear = substr($pay->date,0,7);
            $date = Carbon::parse($monthyear . '-01 00:00:00');

            @$historic[$monthyear]['gcfs'] += $pay->amount;
            @$historic[$monthyear]['date_format'] = $date->format('M Y');
        }*/

        $now = Carbon::now();
        for ($i = 0; $i < 6; $i++)
        {
            $monthyear = $now->format('Y-m');

            $diffInDays = $now->diffInDays(Carbon::now());

            // Is there a serialized monthly report for this subscription and monthyear yet?
            $serializedReport = SerializedMonthlyReport::where([
                'subscription_id' => $this->subscription->id,
                'monthyear' => $monthyear,
                'type' => 'gcfhist'
            ])->first();

            if ($serializedReport)
            {
                $result = unserialize($serializedReport->content);
            } else {
                $result = FSEPayment::month($monthyear)
                    ->group($this->group_id)
                    ->paidto($group->name)
                    ->equalreason(config('fse.stringFboGCF'))
                    ->sum('amount');
            }

            /*$result = DB::table('fse_payments')
                ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
                ->where([
                    ['group_id', '=', $this->group_id],
                    ['p_to', '=', $group->name],
                    ['reason', '=', 'FBO ground crew fee'],
                ])
                ->sum('amount');*/

            $other[] = [
                "y" => $now->format('M Y'),
                "a" => number_format($result,2, '.', ''),
            ];

            $now->modify('-1 month');

            if ($diffInDays > 31)
            {
                // make new report serialization
                SerializedMonthlyReport::updateOrCreate(
                    ['subscription_id' => $this->subscription->id,
                        'group_id' => $this->group_id,
                        'monthyear' => $monthyear,
                        'content' => serialize($result),
                        'type' => 'gcfhist'],
                    ['subscription_id' => $this->subscription->id,
                        'group_id' => $this->group_id,
                        'monthyear' => $monthyear,
                        'content' => serialize($result),
                        'type' => 'gcfhist']);
            }
        }
        return $other;
    }

    public function getHistoricGroupProfit($pilot=null)
    {
        $other = [];

        $now = Carbon::now();
        for ($i = 0; $i < 6; $i++)
        {
            $monthyear = $now->format('Y-m');
            $result = DB::table('fse_flightlogs')
                ->selectRaw('SUM(income-pilot_fee-crew_cost-booking_fee+bonus-fuel_cost-gcf-rental_cost) GroupProfit, SUM(pilot_fee) PilotProfit')
                ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
                ->where('group_id', $this->group_id);

            if ($pilot)
            {
                $result = $result->where('pilot', $pilot)->first();
                $fuel = $this->getFuelExpense($monthyear, $pilot);
                $otherexpense = 0; # if pilot pull, don't factor in maintenance or ownership since those are difficult to distribute
            } else {
                $result = $result->first();

                # regular pull factor in all expenses
                $fuel = $this->getFuelExpense($monthyear);
                $maintenance = $this->getMaintenanceExpense($monthyear);
                $ownership = $this->getOwnershipExpense($monthyear);

                $otherexpense = $maintenance + $ownership;
            }

            $other[] = [
                "y" => $now->format('M Y'),
                "a" => number_format($result->GroupProfit - $fuel - $otherexpense,2, '.', ''),
                "b" => number_format($result->PilotProfit, 2, '.',''),
            ];

            $now->modify('-1 month');
        }
        return $other;
    }

    public function getPersonalFlightReportData()
    {
        $flightlogs = FSEFlightLog::month(session()->get('monthyear'))
            ->group($this->group_id)
            ->where('type', '=', 'flight')
            ->get();

        // Payments
        $payments = FSEPayment::month(session()->get('monthyear'))
            ->group($this->group_id)
            ->get();

        // Fuelling
        $jeta = [];
        $ll = [];
        $gcfsByFBO = [];
        $gcfsByPayer = [];
        foreach ($payments as $payment)
        {
            if (Str::contains($payment->p_from, $this->group->name))
            {
                if (Str::contains($payment->reason, 'Refuelling'))
                {
                    if (Str::contains($payment->reason, 'JetA'))
                    {
                        @$jeta[$payment->p_to]['count']++;
                        @$jeta[$payment->p_to]['amount'] += $payment->amount;
                    }
                    else if (Str::contains($payment->reason, '100LL'))
                    {
                        @$ll[$payment->p_to]['count']++;
                        @$ll[$payment->p_to]['amount'] += $payment->amount;
                    }
                }

                else if (Str::contains($payment->reason, config('fse.stringFboGCF')))
                {
                    @$gcfsByFBO[$payment->fbo]['count']++;
                    @$gcfsByFBO[$payment->fbo]['amount'] += $payment->amount;

                    @$gcfsByPayer[$payment->p_to]['count']++;
                    @$gcfsByPayer[$payment->p_to]['amount'] += $payment->amount;
                }
            }
        }

        //Totals
        $flightsByAircraft = [];
        $flightsByProfitability = [];
        @$totals = [
            'count' => 0,
            'income' => 0,
            'crew_cost' => 0,
            'booking_fee' => 0,
            'bonus' => 0,
            'fuel_cost' => 0,
            'gcf' => 0,
            'rental_cost' => 0,
            'distance' => 0,
            'flight_time' => 0,
            'pilot_fee' => 0,
            'solo_flying' => 0,
            'prof_per_nm' => 0,
            'prof_per_hr' => 0,
            'prof_per_flt' => 0,
        ];

        foreach ($flightlogs as $flight)
        {
            if (empty($flight->group_name))
            {
                @$flightsByAircraft[$flight->aircraft]['count']++;
                @$flightsByAircraft[$flight->aircraft]['income'] += $flight->income;
                @$flightsByAircraft[$flight->aircraft]['crew_cost'] += $flight->crew_cost;
                @$flightsByAircraft[$flight->aircraft]['booking_fee'] += $flight->booking_fee;
                @$flightsByAircraft[$flight->aircraft]['bonus'] += $flight->bonus;
                @$flightsByAircraft[$flight->aircraft]['fuel_cost'] += $flight->fuel_cost;
                @$flightsByAircraft[$flight->aircraft]['gcf'] += $flight->gcf;
                @$flightsByAircraft[$flight->aircraft]['distance'] += $flight->distance;
                @$flightsByAircraft[$flight->aircraft]['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
                @$flightsByAircraft[$flight->aircraft]['rental_units'] += $this->convertTimeToDecimal($flight->rental_units);
                @$flightsByAircraft[$flight->aircraft]['rental_cost'] += $flight->rental_cost;
                @$flightsByAircraft[$flight->aircraft]['make_model'] = $flight->make_model;
                @$flightsByAircraft[$flight->aircraft]['fuel_payments'] = 0;

                @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['count']++;
                @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['pilot_fee'] += $flight->income-$flight->crew_cost-$flight->booking_fee+$flight->bonus-$flight->fuel_cost-$flight->gcf-$flight->rental_cost;

                $totals['count']++;
                $totals['income'] += $flight->income;
                $totals['crew_cost'] += $flight->crew_cost;
                $totals['booking_fee'] += $flight->booking_fee;
                $totals['bonus'] += $flight->bonus;
                $totals['fuel_cost'] += $flight->fuel_cost;
                $totals['gcf'] += $flight->gcf;
                $totals['rental_cost'] += $flight->rental_cost;
                $totals['distance'] += $flight->distance;
                $totals['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
            }
            else if (!empty($flight->group_name))
            {
                @$flightsByAircraft[$flight->aircraft]['count']++;
                @$flightsByAircraft[$flight->aircraft]['group_name'] = $flight->group_name;
                @$flightsByAircraft[$flight->aircraft]['pilot_fee'] += $flight->pilot_fee;
                @$flightsByAircraft[$flight->aircraft]['distance'] += $flight->distance;
                @$flightsByAircraft[$flight->aircraft]['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
                @$flightsByAircraft[$flight->aircraft]['rental_units'] += $this->convertTimeToDecimal($flight->rental_units);
                @$flightsByAircraft[$flight->aircraft]['rental_cost'] += $flight->rental_cost;
                @$flightsByAircraft[$flight->aircraft]['make_model'] = $flight->make_model;
                @$flightsByAircraft[$flight->aircraft]['fuel_payments'] = 0;

                @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['count']++;
                @$flightsByProfitability[$flight->f_from . ' -> ' . $flight->f_to]['pilot_fee'] += $flight->pilot_fee;

                $totals['pilot_fee'] += $flight->pilot_fee;
                $totals['count']++;
                $totals['distance'] += $flight->distance;
                $totals['flight_time'] += $this->convertTimeToDecimal($flight->flight_time);
            }
        }

        $totals['solo_flying'] = $totals['income']-$totals['crew_cost']-$totals['booking_fee']+$totals['bonus']-$totals['fuel_cost']-$totals['gcf']-$totals['rental_cost'];
        $totals['distance'] != 0 ? $totals['prof_per_nm'] = ($totals['solo_flying']+$totals['pilot_fee']) / $totals['distance'] : $totals['prof_per_nm'] = 0;
        $totals['flight_time'] != 0 ? $totals['prof_per_hr'] = ($totals['solo_flying']+$totals['pilot_fee']) / $totals['flight_time'] : $totals['prof_per_hr'] = 0;
        $totals['count'] != 0 ? $totals['prof_per_flt'] = ($totals['solo_flying']+$totals['pilot_fee']) / $totals['count'] : $totals['count'] = 0;

        $result = new \stdClass();
        $result->flightsByAircraft = $flightsByAircraft;
        $result->fuelJetA = $jeta;
        $result->fuel100LL = $ll;
        $result->gcfsByFBO = $gcfsByFBO;
        $result->gcfsByPayer = $gcfsByPayer;
        $result->fuelByPayer = [];
        $result->flightsByProfitability = $flightsByProfitability;
        $result->summaryRow = $totals;

        return $result;
    }

    public function getRentalFleetReportData()
    {
        // Payments
        $payments = FSEPayment::month(session()->get('monthyear'))
            ->group($this->group_id)
            ->get();
    }

    public function convertTimeToDecimal($time)
    {
        $splitTime = explode(':', $time);
        $flt_hour = ($splitTime[0] + ($splitTime[1] / 60));
        if ($flt_hour == 0) { $flt_hour = .001; }

        return $flt_hour;
    }

    public function convertDecimalToTime($decimal)
    {
        $mergeTime = explode('.', $decimal);
        $int_min = isset($mergeTime[1]) ? ".".$mergeTime[1] * 1 : ".0";
        //$int_min = ".".$mergeTime[1] * 1;
        $fmt_int = $int_min * 60;
        $num = round($fmt_int);
        $fmt_min = sprintf("%02s", $num);
        $fmt_time = ($mergeTime[0].":".$fmt_min);

        return $fmt_time;
    }

    public function getHistoricPilotProfit()
    {
        $now = Carbon::now();
        $sixMonthsAgo = $now->copy()->modify('-6 months');

        $flightlog = FSEFlightLog::monthwindow($sixMonthsAgo->format('Y-m'), $now->format('Y-m'))
            ->group($this->group_id)
            ->where('type', '=', 'flight')
            ->get();

        $historic = [];

        foreach ($flightlog as $flight)
        {
            $monthyear = substr($flight->date,0,7);
            $date = Carbon::parse($monthyear . '-01 00:00:00');

            @$historic[$monthyear]['revenue'] += $flight->income + $flight->pilot_fee;
            @$historic[$monthyear]['expense'] += $flight->crew_cost + $flight->booking_fee + $flight->bonus + $flight->fuel_cost + $flight->gcf + $flight->rental_cost;
            @$historic[$monthyear]['profit'] = $historic[$monthyear]['revenue'] - $historic[$monthyear]['expense'];
            @$historic[$monthyear]['date_format'] = $date->format('M Y');
        }

        return $this->formMorrisBarDataPilotProf($historic);
    }

    public function getFlightsByPilot()
    {
        $totals = ['totalgroup' => 0, 'totalpilot' => 0, 'totalprofit' => 0];
        $pilottotal = array();

        $result = DB::table('fse_flightlogs')
            ->selectRaw('pilot, SUM(pilot_fee) "PilotProfit", SUM(income-pilot_fee-crew_cost-booking_fee+bonus-fuel_cost-gcf-rental_cost) "GroupProfit", COUNT(*) "TotalFlights", SUM(distance) "TotalDistance"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where('group_id', $this->group_id)
            ->groupBy('pilot')
            ->orderByDesc('GroupProfit')
            ->get();

        foreach ($result as $row) {
            // more logic here to parse userid to get pilot name
            $fuelcost = $this->getPilotFuelExpense($row->pilot);
            $totalflighttime = $this->getTotalFlightTime('pilot', $row->pilot);

            $row->fuelcost = $fuelcost;
            $row->totalflighttime = $totalflighttime;

            $pilottotal[$row->pilot] = $row->GroupProfit - $fuelcost;

            $totals['totalgroup'] += $row->GroupProfit - $fuelcost;
            $totals['totalpilot'] += $row->PilotProfit;
        }

        $totals['totalprofit'] = $totals['totalgroup'] + $totals['totalpilot'];

        // For highcharts
        foreach ($result as $row) {
            $data[] = "{name: '".$row->pilot."', y: ".$pilottotal[$row->pilot]/$totals['totalgroup'].'}';
        }

        if ($result) {
            return array('success' => true, 'message' => $result, 'totals' => $totals, 'highcharts' => $data);
        }
    }

    public function getFlightsByAircraft($pilot=null)
    {
        $result = DB::table('fse_flightlogs')
            ->selectRaw('aircraft, make_model, serial_number, SUM(income-pilot_fee-crew_cost-booking_fee+bonus-fuel_cost-gcf-rental_cost) "GroupProfit", SUM(pilot_fee) "PilotProfit", SUM(distance) "TotalDistance"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where('group_id', $this->group_id)
            ->groupBy(['aircraft', 'make_model', 'serial_number'])
            ->orderByDesc('GroupProfit');

        if ($pilot)
        {
            $result = $result->where('pilot', $pilot)->get();
        }
        else
        {
            $result = $result->get();
        }

        foreach ($result as $row) {
            $fuelcost = $this->getAircraftFuelExpense($row->aircraft);
            $totalflighttime = $this->getTotalFlightTime('aircraft', $row->aircraft);

            $row->fuelcost = $fuelcost;
            $row->totalflighttime = $totalflighttime;
        }

        //dd($result);

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getPilotFuelExpense($pilot)
    {
        // Get group name
        $group = Group::find($this->group_id);

        // Get fseid
        $fseid = $this->getPilotFSEId($pilot);

        $fuelcost = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_from', '=', $group->name],
                ['comment', 'like', '%'.$fseid.'%'],
                ['reason', 'like', '%Refuelling%'],
            ])
            ->sum('amount');

        return $fuelcost;
    }

    public function getAircraftFuelExpense($aircraft)
    {
        $group = Group::find($this->group_id);

        $fuelcost = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_from', '=', $group->name],
                ['aircraft', '=', $aircraft],
                ['reason', 'like', '%Refuelling%'],
            ])
            ->sum('amount');

        return $fuelcost;
    }

    public function getFuelExpense($monthyear, $pilot=null)
    {
        $reportMonthYear = Carbon::parse($monthyear);
        $diffInDays = Carbon::now()->diffInDays($reportMonthYear);

        // Is there a serialized monthly report for this subscription and monthyear yet?
        $serializedReport = SerializedMonthlyReport::where([
            'subscription_id' => $this->subscription->id,
            'monthyear' => $monthyear,
            'type' => 'fuelexp'
        ])->first();

        if ($serializedReport) {
            $fuelcost = unserialize($serializedReport->content);
        }
        else
        {
            $fuelcost = DB::table('fse_payments')
                ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
                ->where([
                    ['group_id', '=', $this->group_id],
                    ['p_from', '=', $this->group->name],
                    ['reason', 'like', '%Refuelling%'],
                ]);

            if ($pilot)
            {
                $fseid = $this->getPilotFSEId($pilot);
                $fuelcost = $fuelcost->where('comment', 'like','%'.$fseid.'%')->sum('amount');
            }
            else
            {
                $fuelcost = $fuelcost->sum('amount');
            }
        }

        if ($diffInDays > 31)
        {
            // make new report serialization
            SerializedMonthlyReport::updateOrCreate(
                ['subscription_id' => $this->subscription->id,
                    'group_id' => $this->group_id,
                    'monthyear' => $monthyear,
                    'content' => serialize($fuelcost),
                    'type' => 'fuelexp'],
                ['subscription_id' => $this->subscription->id,
                    'group_id' => $this->group_id,
                    'monthyear' => $monthyear,
                    'content' => serialize($fuelcost),
                    'type' => 'fuelexp']);
        }

        return $fuelcost;
    }

    public function getMaintenanceExpense($monthyear)
    {
        $group = Group::find($this->group_id);

        $maintenance = DB::table('fse_payments')
            ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_from', '=', $group->name],
                ['reason', '=', 'Aircraft maintenance'],
            ])
            ->sum('amount');

        return $maintenance;
    }

    public function getOwnershipExpense($monthyear)
    {
        $group = Group::find($this->group_id);

        $ownership = DB::table('fse_payments')
            ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_from', '=', $group->name],
                ['reason', '=', config('fse.stringOwnershipFee')],
            ])
            ->sum('amount');

        return $ownership;
    }

    public function getPilotFSEId($pilot)
    {
        $result = DB::table('fseid_username')->where('username', $pilot)->first();

        if ($result) {
            $fseid = $result->id;
        } else {
            // A row does not exist
            // Hit the API and insert that record
            $fseApi = new FSEApi();

            $fseid = $fseApi->getFSEID($pilot);

            DB::table('fseid_username')->insert([
                'id' => $fseid,
                'username' => $pilot
            ]);
        }

        return $fseid;
    }

    public function getTotalFlightTime($pilotoraircraft, $pilotaircraft)
    {
        $tot_hour = 0;

        $result = DB::table('fse_flightlogs')
            ->select('flight_time')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where('group_id', $this->group_id)
            ->where($pilotoraircraft, $pilotaircraft)
            ->get();

        //dd($result);

        foreach ($result as $row) {
            // CONVERT TIME STRING AND ADD FLIGHT TIMES
            $splitTime = explode(':', $row->flight_time);
            $flt_hour = ($splitTime[0] + ($splitTime[1] / 60));
            if ($flt_hour == 0) { $flt_hour = .001; }
            $tot_hour += $flt_hour;
        }

        // CONVERT TOTAL FLIGHT TIME BACK TO HHH:MM
        $mergeTime = explode('.', $tot_hour);
        $int_min = isset($mergeTime[1]) ? ".".$mergeTime[1] * 1 : ".0";
        //$int_min = ".".$mergeTime[1] * 1;
        $fmt_int = $int_min * 60;
        $num = round($fmt_int);
        $fmt_min = sprintf("%02s", $num);
        $fmt_time = ($mergeTime[0].":".$fmt_min);

        return $fmt_time;
    }

    public function getGCFsPaidByFBO()
    {
        //$group = Group::find($this->group_id);

        $result = DB::table('fse_payments')
            ->selectRaw('fbo, SUM(amount) "Total", COUNT(*) "GCFs", ROUND(SUM(amount)/COUNT(*), 2) "AverageGCF"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%ground%'],
                ['p_from', '=', $this->group->name],
            ])
            ->groupBy('fbo')
            ->orderByDesc('Total')
            ->get();

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getGCFsGenByFBO()
    {
        $group = Group::find($this->group_id);

        $result = DB::table('fse_payments')
            ->selectRaw('fbo, SUM(amount) "Total", COUNT(*) "GCFs", ROUND(SUM(amount)/COUNT(*), 2) "AverageGCF"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%ground%'],
                ['p_to', '=', $group->name],
            ])
            ->groupBy('fbo')
            ->orderByDesc('Total')
            ->get();

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getGCFsByPayer()
    {
        $result = DB::table('fse_payments')
            ->selectRaw('p_to, SUM(amount) "Total", COUNT(*) "GCFs", ROUND(SUM(amount)/COUNT(*), 2) "AverageGCF"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%ground%'],
            ])
            ->groupBy('p_to')
            ->orderByDesc('Total')
            ->get();

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getGCFsByPayee()
    {
        $result = DB::table('fse_payments')
            ->selectRaw('p_from, SUM(amount) "Total", COUNT(*) "GCFs", ROUND(SUM(amount)/COUNT(*), 2) "AverageGCF"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%ground%'],
            ])
            ->groupBy('p_from')
            ->orderByDesc('Total')
            ->get();

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getRefuelRevByFBO()
    {
        $totals = 0;

        $result = DB::table('fse_payments')
            ->selectRaw('fbo, SUM(amount) "Total"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuelling%'],
            ])
            ->groupBy('fbo')
            ->orderByDesc('Total')
            ->get();

        foreach ($result as $row) {
            $totals += $row->Total;
        }

        if ($result) {
            return array('success' => true, 'message' => $result, 'totals' => $totals);
        }
    }

    public function getRefuelRevByAircraft($pilot=null)
    {
        $totals = 0;

        $result = DB::table('fse_payments')
            ->selectRaw('aircraft, fbo, SUM(amount) "Total", count(*) "count"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuelling%'],
            ])
            ->groupBy(DB::raw('aircraft,fbo'))
            ->orderByDesc('Total');

        if ($pilot)
        {
            $fseid = $this->getPilotFSEId($pilot);
            $result = $result->where('comment', 'like', '%'.$fseid.'%')->get();
        }
        else {
            $result = $result->get();
        }

        foreach ($result as $row) {
            $totals += $row->Total;
        }

        if ($result) {
            return array('success' => true, 'message' => $result, 'totals' => $totals);
        }
    }

    public function getRefuelRevByFBOType($type)
    {
        $totals = array('rev' => 0, 'gal' => 0, 'cost' => 0, 'prof' => 0);

        $result = DB::table('fse_payments')
            ->selectRaw('fbo, SUM(amount) "Total"')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuelling%'],
                ['reason', 'like', '%'.$type.'%'],
            ])
            ->groupBy('fbo')
            ->orderByDesc('Total')
            ->get();

        foreach ($result as $row) {
            $fbo = $row->fbo;

            $g = $this->getGallonsSold($fbo, $type);

            $row->gallonssold = $g['totalgallonssold'];
            $row->fuelcost = $g['totalfuelcost'];
            $row->fuelprofit = $g['totalfuelprofit'];

            $totals['rev'] += $row->Total;
            $totals['gal'] += $row->gallonssold;
            $totals['cost'] += $row->fuelcost;
            $totals['prof'] += $row->fuelprofit;
        }

        if ($result) {
            return array('success' => true, 'message' => $result, 'totals' => $totals);
        }
    }

    public function getGallonsSold($fbo, $type)
    {
        $totals = array('totalgallonssold' => 0, 'totalfuelcost' => 0, 'totalfuelprofit' => 0);

        $result = DB::table('fse_payments')
            ->selectRaw('amount, comment')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['fbo', '=', $fbo],
                ['reason', 'like', '%refuel%'],
                ['reason', 'like', '%'.$type.'%'],
            ])
            ->get();

        $type == '100ll' ? $wholesale = 3.41 : $wholesale = 3.14;

        foreach ($result as $row) {
            $gallonssold = $this->findGallons($row->comment);

            $fuelcost = $gallonssold * $wholesale;
            $fuelprofit = $row->amount - $fuelcost;

            $totals['totalgallonssold'] += $gallonssold;
            $totals['totalfuelcost'] += $fuelcost;
            $totals['totalfuelprofit'] += $fuelprofit;
        }

        return $totals;
    }

    public function getMaintenanceRevenue()
    {
        $group = Group::find($this->group_id);

        $result = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_to', '=', $group->name],
                ['reason', '=', 'Aircraft maintenance'],
            ])
            ->sum('amount');

        return $result;
    }

    public function getMaintenanceCost()
    {
        $group = Group::find($this->group_id);

        $result = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['p_from', '=', $group->name],
                ['reason', 'like', '%cost%'],
                ['reason', 'like', '%maintenance%'],
            ])
            ->sum('amount');

        return $result;
    }

    public function findGallons($data) {
        $string = explode(':', $data);

        $userid = explode(' ', trim($string[1]));
        $userid = trim($userid[0]);

        $gallons = explode(',', $string[2]);
        $gallons = trim($gallons[0]);

        $pricepergallon = trim($string[3]);

        return $gallons;
    }

    public function getFuelPaymentsByPayer($pilot=null) {
        $result = DB::table('fse_payments')
            ->selectRaw('p_to as paidto, SUM(amount) as total, COUNT(*) as count, ROUND(SUM(amount)/COUNT(*), 2) as avgpmt')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuel%'],
            ])
            ->groupBy('p_to')
            ->orderByDesc('total');

        if ($pilot)
        {
            $fseid = $this->getPilotFSEId($pilot);
            $result = $result->where('comment', 'like', '%'.$fseid.'%')->get();
        }
        else
        {
            $result = $result->get();
        }

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getSumJetAPayments() {
        $result = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuel%'],
                ['reason', 'like', '%jeta%']
            ])
            ->sum('amount');

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getSum100LLPayments() {
        $result = DB::table('fse_payments')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuel%'],
                ['reason', 'like', '%100ll%']
            ])
            ->sum('amount');

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getAllJetAPayments($pilot=null) {
        $result = DB::table('fse_payments')
            ->selectRaw('p_to as paidto, SUM(amount) as total, COUNT(*) as count, ROUND(SUM(amount)/COUNT(*), 2) as avgpmt')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuel%'],
                ['reason', 'like', '%jeta%'],
            ])
            ->groupBy('p_to')
            ->orderByDesc('total');

        if ($pilot)
        {
            $fseid = $this->getPilotFSEId($pilot);
            $result = $result->where('comment', 'like', '%'.$fseid.'%')->get();
        }
        else {
            $result = $result->get();
        }

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getAll100LLPayments($pilot=null) {
        $result = DB::table('fse_payments')
            ->selectRaw('p_to as paidto, SUM(amount) as total, COUNT(*) as count, ROUND(SUM(amount)/COUNT(*), 2) as avgpmt')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where([
                ['group_id', '=', $this->group_id],
                ['reason', 'like', '%refuel%'],
                ['reason', 'like', '%100ll%'],
            ])
            ->groupBy('p_to')
            ->orderByDesc('total');

        if ($pilot)
        {
            $fseid = $this->getPilotFSEId($pilot);
            $result = $result->where('comment', 'like', '%'.$fseid.'%')->get();
        }
        else {
            $result = $result->get();
        }

        if ($result) {
            return array('success' => true, 'message' => $result);
        }
    }

    public function getHistoricRefuellingRev()
    {
        $other = [];

        $group = Group::find($this->group_id);

        $now = Carbon::now();
        for ($i = 0; $i < 6; $i++)
        {
            $monthyear = $now->format('Y-m');
            $result = DB::table('fse_payments')
                ->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59'])
                ->where([
                    ['group_id', '=', $this->group_id],
                    ['p_to', '=', $group->name],
                    ['reason', 'like', '%Refuelling%'],
                ])
                ->sum('amount');

            $other[] = [
                "y" => $now->format('M Y'),
                "a" => number_format($result,2, '.', ''),
            ];

            $now->modify('-1 month');
        }

        return $other;
    }

    public function getAircraftFlightCount()
    {
        $other = [];

        $result = DB::table('fse_flightlogs')
            ->selectRaw('serial_number, aircraft, make_model, COUNT(*) as count')
            ->whereBetween('date', [session()->get('monthyear') . '-01 00:00:00', session()->get('monthyear') . '-31 23:59:59'])
            ->where('group_id', $this->group_id)
            ->groupBy(['serial_number', 'aircraft', 'make_model'])
            ->get();

        foreach ($result as $r)
        {
            $other[] = [
                "label" => $r->aircraft . '\n(' . $r->make_model . ')\n',
                "value" => $r->count,
            ];
        }

        return $other;
    }

    public function formMorrisDonutData($data)
    {
        $string = '';
        foreach ($data as $k)
        {
            $string .= '{ label: "' . $k["label"] . '", value: ' . $k["value"] . '},';
        }

        return rtrim($string, ',');
    }

    public function formMorrisBarDataProf($data)
    {
        $string = '';
        foreach ($data as $k => $v)
        {
            $string .= '{y: "'. $v["y"] . '", a: ' . $v["a"] . ', b: "' . $v["b"] . '"},';
        }

        return rtrim($string, ',');
    }

    public function formMorrisBarDataPilotProf($data)
    {
        $string = '';
        foreach ($data as $k => $v)
        {
            $string .= '{y: "'. $v['date_format']. '", a: "' . $v["profit"] . '"},';
        }

        return rtrim($string, ',');
    }

    public function formMorrisBarDataGCF($data)
    {
        $string = '';
        foreach ($data as $k => $v)
        {
            $string .= '{y: "'. $v["y"] . '", a: ' . $v["a"] . '},';
        }

        return rtrim($string, ',');
    }

    public function lastSixMonthsForPL()
    {
        // Last six FULL months
        $res = [];

        $now = Carbon::now();
        $now->modify('-1 month');

        for ($i = 0; $i < 6; $i++)
        {
            $now->modify('-1 month');

            $monthyear = $now->format('M Y');

            $res[] = $monthyear;
        }

        return array_reverse($res);
    }

    public function lastNumMonthsForOneFBO(FseFbo $fbo, $numberOfMonths)
    {
        $res = [];

        $group = Group::find($this->group_id);

        $now = Carbon::now();
        //$now->modify('-1 month');

        $data = [];

        for ($i = 0; $i < $numberOfMonths; $i++) {
            $now->modify('-1 month');

            $month = $now->format('m');
            $year = $now->format('Y');

            // Get supplies cost
            $daysinmonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $supplycost = $fbo->supplies_per_day * $daysinmonth * config('fse.suppliesPricePerKg');

            //Totals
            $jetaprof = 0;
            $llprof = 0;
            $gcfrev = 0;
            $maintrev = 0;
            $maintcost = 0;
            $equiprev = 0;
            $equipcost = 0;
            $paxtermprofit = 0;

            $payments = FSEPayment::month($now->format('Y-m'))
                ->group($this->group_id)
                ->fbo($fbo->icao . ' ' . $fbo->name)
                ->get();

            foreach ($payments as $payment)
            {
                if (Str::contains($payment->p_to, $group->name))
                {
                    if (Str::contains($payment->reason, 'Refuelling'))
                    {
                        $rev = $payment->amount;
                        $comment = $payment->comment;
                        $gallonsSold = $this->findGallons($comment);

                        if (Str::contains($payment->reason, 'JetA'))
                        {
                            $profit = $rev - ($gallonsSold * config('fse.jetACostPerGallon'));
                            $jetaprof += $profit;
                        }
                        else if (Str::contains($payment->reason, '100LL'))
                        {
                            $profit = $rev - ($gallonsSold * config('fse.llCostPerGallon'));
                            $llprof += $profit;
                        }
                    }

                    else if (Str::contains($payment->reason, config('fse.stringFboGCF')))
                    {
                        $gcfrev += $payment->amount;
                    }

                    else if (Str::contains($payment->reason, config('fse.stringAircraftMaintenance')))
                    {
                        $maintrev += $payment->amount;
                    }

                    else if (Str::contains($payment->reason, 'equipment') && Str::contains($payment->reason, 'aircraft'))
                    {
                        $equiprev += $payment->amount;
                    }

                    else if(Str::contains($payment->reason, 'facility') && Str::contains($payment->reason, 'rent'))
                    {
                        $paxtermprofit += $payment->amount;
                    }

                }

                else if (Str::contains($payment->p_from, $group->name))
                {
                    if (Str::contains($payment->reason, 'Cost') && Str::contains($payment->reason, 'maintenance'))
                    {
                        $maintcost += $payment->amount;
                    }
                    else if(Str::contains($payment->reason, 'Cost') && Str::contains($payment->reason, 'equipment'))
                    {
                        $equipcost += $payment->amount;
                    }
                }
            }

            $refuellingprofit = $llprof + $jetaprof;
            $maintprofit = $maintrev - $maintcost;
            $equipprofit = $equiprev - $equipcost;

            // Get wholesale goods prof !!!!!!!!!!!!!!!!
            // unable to discriminate between regular purchase (to fly fuel in) and wholesale purchase (ordering fuel/supplies)

            $net = $gcfrev - $supplycost + $refuellingprofit + $maintprofit + $equipprofit + $paxtermprofit;

            $data[] = [
                'month_year' => $now->format('M') . " $year",
                'supply_cost' => '$'.number_format($supplycost, 2),
                'gcf_revenue' => '$'.number_format($gcfrev, 2),
                'refuel_profit' => '$'.number_format($refuellingprofit, 2),
                'maintenance_profit' => '$'.number_format($maintprofit, 2),
                'equip_profit' => '$'.number_format($equipprofit, 2),
                'pt_rent' => '$'.number_format($paxtermprofit, 2),
                'net' => $net,
            ];
        }

        $sumall = [];
        foreach ($data as $d)
        {
            @$sumall['supply_cost'] += str_replace(str_split(",$"), '', $d['supply_cost']);
            @$sumall['gcf_revenue'] += str_replace(str_split(",$"), '', $d['gcf_revenue']);
            @$sumall['refuel_profit'] += str_replace(str_split(",$"), '', $d['refuel_profit']);
            @$sumall['maintenance_profit'] += str_replace(str_split(",$"), '', $d['maintenance_profit']);
            @$sumall['equip_profit'] += str_replace(str_split(",$"), '', $d['equip_profit']);
            @$sumall['pt_rent'] += str_replace(str_split(",$"), '', $d['pt_rent']);
            @$sumall['net'] += str_replace(str_split(",$"), '', $d['net']);
            @$sumall['month_year'] = 'Total';
        }

        $sumall['supply_cost'] = '$'.number_format($sumall['supply_cost'], 2);
        $sumall['gcf_revenue'] = '$'.number_format($sumall['gcf_revenue'], 2);
        $sumall['refuel_profit'] = '$'.number_format($sumall['refuel_profit'], 2);
        $sumall['maintenance_profit'] = '$'.number_format($sumall['maintenance_profit'], 2);
        $sumall['equip_profit'] = '$'.number_format($sumall['equip_profit'], 2);
        $sumall['pt_rent'] = '$'.number_format($sumall['pt_rent'], 2);

        $res = array_reverse($data);

        $res[] = $sumall;

        return $res;
    }

    public function lastSixMonthsForPLData()
    {
        $res = [];

        $group = Group::find($this->group_id);

        $fbos = DB::table('fse_fbos')
            ->where('group_id', $this->group_id)
            ->get();

        foreach ($fbos as $f) {
            $now = Carbon::now();
            $now->modify('-1 month');

            $data = [];

            //echo $f->name . ' ' . $f->icao . '<br>';
            $fboname = $f->icao . ' ' . $f->name;

            for ($i = 0; $i < 6; $i++) {
                $now->modify('-1 month');

                $month = $now->format('m');
                $year = $now->format('Y');

                // Get supplies cost
                $daysinmonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $supplycost = $f->supplies_per_day * $daysinmonth * 5.0625; // supplies are always bought at this price per kg

                // Get GCF rev
                $gcfrev = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', '=', 'FBO ground crew fee'],
                    ])
                    ->sum('amount');

                // Get fuel profit !!!!!!!!!!!!!!!
                // JetA
                $jetafuelpayments = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', 'like', '%refuelling%'],
                        ['reason', 'like', '%jeta%'],
                    ])
                    ->get();

                $jetacost = 3.14;
                $jetaprof = 0;
                foreach ($jetafuelpayments as $p)
                {
                    $rev = $p->amount;
                    $comment = $p->comment;
                    $gallonsSold = $this->findGallons($comment);
                    $jetaprof += $rev - ($gallonsSold * $jetacost);
                }

                // 100LL
                $llfuelpayments = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', 'like', '%refuelling%'],
                        ['reason', 'like', '%100ll%'],
                    ])
                    ->get();

                $llcost = 3.41;
                $llprof = 0;
                foreach ($llfuelpayments as $p)
                {
                    $rev = $p->amount;
                    $comment = $p->comment;
                    $gallonsSold = $this->findGallons($comment);
                    $llprof += $rev - ($gallonsSold * $llcost);
                }
                $refuellingprofit = $llprof + $jetaprof;

                // Get maintenance prof
                $maintrev = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', '=', 'Aircraft maintenance'],
                    ])
                    ->sum('amount');

                $maintcost = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['reason', 'like', '%cost%'],
                        ['reason', 'like', '%maintenance%'],
                    ])
                    ->sum('amount');
                $maintprofit = $maintrev - $maintcost;

                // Get equip profit !!!!!!!!!!!!!!!
                $equiprev = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', 'like', '%equipment%'],
                        ['reason', 'like', '%aircraft%'],
                    ])
                    ->sum('amount');

                $equipcost = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '!=', $group->name],
                        ['reason', 'like', '%cost%'],
                        ['reason', 'like', '%equipment%'],
                    ])
                    ->sum('amount');
                $equipprofit = $equiprev - $equipcost;

                // Get pax terminal prof !!!!!!!!!!!!!
                $paxtermprofit = DB::table('fse_payments')
                    ->whereBetween('date', [$now->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59'])
                    ->where([
                        ['group_id', '=', $this->group_id],
                        ['fbo', '=', $fboname],
                        ['p_to', '=', $group->name],
                        ['reason', 'like', '%facility%'],
                        ['reason', 'like', '%rent%'],
                    ])
                    ->sum('amount');

                // Get wholesale goods prof !!!!!!!!!!!!!!!!
                // unable to discriminate between regular purchase (to fly fuel in) and wholesale purchase (ordering fuel/supplies)

                $net = $gcfrev - $supplycost + $refuellingprofit + $maintprofit + $equipprofit + $paxtermprofit;

                /*echo $month . '/' . $year . '<br>';
                echo 'Monthly Supply Cost: $' . $supplycost . '<br>';
                echo 'Monthly GCF Revenue: $' . $gcfrev. '<br>';
                echo 'Monthly Refuelling Profit: $' . $refuellingprofit. '<br>';
                echo 'Monthly Maintenance Profit: $' . $maintprofit. '<br>';
                echo 'Monthly Equipment Installation/Remove Profit: $' . $equipprofit. '<br>';
                echo 'Monthly PT Rent Profit: $' . $paxtermprofit. '<br>';
                echo 'FBO NET: ' . number_format($net,2) . '<br>';
                echo '<br><br>';*/

                $data[] = [
                    'month_year' => "$month $year",
                    'supply_cost' => (float) $supplycost,
                    'gcf_revenue' => (float) $gcfrev,
                    'refuel_profit' => (float) $refuellingprofit,
                    'maintenance_profit' => (float) $maintprofit,
                    'equip_profit' => (float) $equipprofit,
                    'pt_rent' => (float) $paxtermprofit,
                    'net' => (float) $net,
                ];
            }

            $res[$fboname] = array_reverse($data);
        }

        return $res;
    }
}