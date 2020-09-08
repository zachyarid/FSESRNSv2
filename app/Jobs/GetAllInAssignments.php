<?php

namespace App\Jobs;

use App\FSEAssignment;
use App\Libraries\FSEData;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetAllInAssignments implements ShouldQueue
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
     * @throws \Exception
     */
    public function handle()
    {
        Log::debug('Assignments: Starting All-In Assignment Pull');

        DB::table('fse_assignments')->where('type','=', 'All-In')->delete();

        $fseData = new FSEData();
        $icaoString = "";
        $numberToGet = 20;
        $i = 0;


        //$aircraft = ["Boeing+737-800", "Airbus+A320", "Airbus+A321", "Boeing+747-400"];
        $aircraft = ["Boeing+737-800"];
        $aircraftData = $fseData->getAircraftByMakeModel($aircraft[0]);
        Log::debug($aircraftData);
        foreach ($aircraftData as $ac)
        {
            if ($i < $numberToGet)
            {
                $fuel = (float)$ac->FuelPct;
                // Don't want to worry about refuelling or rented planes
                if ($fuel > 0.35 && $ac->RentedBy == 'Not rented.')
                {
                    $location = $ac->Location;
                    $icaoString .= $location . '-';

                    $i++;
                }
            }
        }

        $icaoString = rtrim($icaoString, '-');

        sleep(2);

        $assignmentData = $fseData->getAssignments($icaoString);
        foreach ($assignmentData as $assignment)
        {
            if ($assignment->Type == 'All-In' && $assignment->Pay > 5000)
            {
                FSEAssignment::create([
                    'id' => $assignment->Id,
                    'location' => $assignment->Location,
                    'to_icao' => $assignment->ToIcao,
                    'from_icao' => $assignment->FromIcao,
                    'amount' => $assignment->Amount,
                    'unit_type' => $assignment->UnitType,
                    'commodity' => $assignment->Commodity,
                    'pay' => $assignment->Pay,
                    'expires' => $assignment->Expires,
                    'expires_timestamp' => $assignment->ExpireDateTime,
                    'type' => $assignment->Type,
                    'express' => $assignment->Express == 'False' ? 0 : 1,
                    'pt_assignment' => $assignment->PtAssignment,
                    'aircraft_id' => $assignment->AircraftId,
                    'consumed' => 0
                ]);
            }
        }

        Log::debug('Assignments: Ending All-In Assignment Pull');
    }
}
