<?php

namespace App\Jobs;

use App\FSEFlightLog;
use App\Group;
use App\Libraries\FSEData;
use App\MonthPull;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetFSEFlightLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('FlightLogs: Starting Flight Log pull');

        $groups = Group::where('cron_disable', 0)->get();
        $fseData = new FSEData();

        foreach ($groups as $g)
        {
            Log::debug('FlightLogs: Group ' . $g->name);
            $didMonthPull = false;
            $monthpull = $this->getLastMonthPullSet($g->id);

            // determine month or by ID
            if ($monthpull['message'] > 0 || $monthpull['rows'] < 6) {
                $mp = $this->getMPDate($g->id);
                $flightlogs = $fseData->getFlightLogsMonth($g->access_key, $mp['month'], $mp['year']);
                $didMonthPull = true;
            } elseif ($monthpull['message'] == 0) {
                $maxID = $this->getMaxID($g->id);
                $flightlogs = $fseData->getFlightLogs($g->access_key, $maxID);
            } else {
                $mp = $this->getMPDate($g->id);
                $flightlogs = $fseData->getFlightLogsMonth($g->access_key, $mp['month'], $mp['year']);
                $didMonthPull = true;
            }

            $didMonthPull ? Log::debug('FlightLogs: Month Pull enabled') : null;
            $didMonthPull ? Log::debug('FlightLogs: ' . $mp['month'] . '-' . $mp['year']) : null;

            if ($flightlogs['mp_only'])
            {
                if ($didMonthPull)
                {
                    // Insert Month Pull Record
                    MonthPull::create([
                        'group_id' => $g->id,
                        'type' => 'F',
                        'month_pulled' => $mp['year'] . '-' . $mp['month'] . '-01',
                        'completed' => Carbon::now(),
                        'records_inserted' => 0
                    ]);
                }
            }
            else if ($flightlogs['success'])
            {
                $count = 0;
                foreach ($flightlogs['message']->FlightLog as $flightlog)
                {
                    FSEFlightLog::create([
                        'group_id' => $g->id,
                        'fse_id' => $flightlog->Id,
                        'type' => $flightlog->Type,
                        'date' => $flightlog->Time,
                        'distance' => $flightlog->Distance,
                        'pilot' => $flightlog->Pilot,
                        'serial_number' => $flightlog->SerialNumber,
                        'aircraft' => $flightlog->Aircraft,
                        'make_model' => $flightlog->MakeModel,
                        'f_from' => $flightlog->From,
                        'f_to' => $flightlog->To,
                        'total_engine_time' => $flightlog->TotalEngineTime,
                        'flight_time' => $flightlog->FlightTime,
                        'group_name' => $flightlog->GroupName,
                        'income' => $flightlog->Income,
                        'pilot_fee' => $flightlog->PilotFee,
                        'crew_cost' => $flightlog->CrewCost,
                        'booking_fee' => $flightlog->BookingFee,
                        'bonus' => $flightlog->Bonus,
                        'fuel_cost' => $flightlog->FuelCost,
                        'gcf' => $flightlog->GCF,
                        'rental_price' => $flightlog->RentalPrice,
                        'rental_units' => $flightlog->RentalUnits,
                        'rental_cost' => $flightlog->RentalCost
                    ]);
                    $count++;
                }

                Log::debug('FlightLogs: Inserted ' . $count . ' flight logs for group ' . $g->name);

                if ($didMonthPull)
                {
                    // Insert Month Pull Record
                    MonthPull::create([
                        'group_id' => $g->id,
                        'type' => 'F',
                        'month_pulled' => $mp['year'] . '-' . $mp['month'] . '-01',
                        'completed' => Carbon::now(),
                        'records_inserted' => $count
                    ]);
                }
            }
            else
            {
                Log::debug('FlightLogs: ' . $flightlogs['message']);
            }

            sleep(2);
        }

        Log::debug('FlightLogs: Ending Flight Log pull');
    }

    public function getMaxID($groupID)
    {
        return DB::table('fse_flightlogs')
                ->where('group_id', $groupID)
                ->max('fse_id') ?? 0;
    }

    public function getMPDate($group_id)
    {
        // Get previous month pull from month_pull
        $result = DB::table('month_pull')
            ->where([
                ['group_id', '=', $group_id],
                ['type', '=', 'F'],
            ])
            ->limit(1)
            ->orderByDesc('id')
            ->first();

        // If no rows exist, no single month has been pulled.
        // Start with the current month
        if ($result) {
            // If a row does exist, decrement it to pull the previous month
            $now = Carbon::parse($result->month_pulled);
            $now->modify(' -1 month');
        } else {
            $now = Carbon::now();
        }

        return array('month' => $now->format('m'), 'year' => $now->format('Y'));
    }

    public function getLastMonthPullSet($group_id)
    {
        $result = DB::table('month_pull')
            ->select('records_inserted')
            ->where([
                ['group_id', '=', $group_id],
                ['type', '=', 'F'],
            ])
            ->limit(6)
            ->orderByDesc('id')
            ->get();

        $total = 0;
        foreach ($result as $row) {
            $total += $row->records_inserted;
        }

        if ($result) {
            return array('success' => true, 'message' => $total, 'rows' => count($result));
        } else {
            return array('success' => false, 'message' => $this->db->error());
        }

    }
}