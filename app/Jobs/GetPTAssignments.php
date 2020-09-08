<?php

namespace App\Jobs;

use App\FSEAssignment;
use App\Libraries\FSEData;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GetPTAssignments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Assignments: Starting PT Assignment Pull');

        //DB::table('fse_assignments')->where('type','=', 'All-In')->delete();

        $fseData = new FSEData();
        $icaos = "KCLT";

        $assignmentData = $fseData->getAssignments($icaos);
        foreach ($assignmentData as $assignment)
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
            ]);
        }

        Log::debug('Assignments: Ending PT Assignment Pull');
    }
}
