<?php

namespace App\Jobs;

use App\FSEAircraftConfig;
use App\Libraries\FSEData;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GetFSEAircraftConfig implements ShouldQueue
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
        Log::debug('AircraftConfig: Starting FBO pull');

        $fseData = new FSEData();

        $aircraftConfig = $fseData->getAircraftConfig();

        if ($aircraftConfig['success'])
        {
            foreach ($aircraftConfig['message']->AircraftConfig as $config)
            {
                FSEAircraftConfig::updateOrCreate(
                    [
                        'make_model' => $config->MakeModel,
                        'crew' => $config->Crew,
                        'seats' => $config->Seats,
                        'cruise_speed' => $config->CruiseSpeed,
                        'gph' => $config->GPH,
                        'fuel_type' => $config->FuelType,
                        'mtow' => $config->MTOW,
                        'empty_weight' => $config->EmptyWeight,
                        'price' => $config->Price,
                        'ext1' => $config->Ext1,
                        'ltip' => $config->LTip,
                        'laux' => $config->LAux,
                        'lmain' => $config->LMain,
                        'center1' => $config->Center1,
                        'center2' => $config->Center2,
                        'center3' => $config->Center3,
                        'rmain' => $config->RMain,
                        'raux' => $config->RAux,
                        'rtip' => $config->RTip,
                        'ext2' => $config->Ext2,
                        'engines' => $config->Engines,
                        'engine_price' => $config->EnginePrice,
                        'model_id' => $config->ModelId,
                        'alias' => '',
                        'enabled' => 0
                    ],
                    [
                        'crew' => $config->Crew,
                        'seats' => $config->Seats,
                        'cruise_speed' => $config->CruiseSpeed,
                        'gph' => $config->GPH,
                        'fuel_type' => $config->FuelType,
                        'mtow' => $config->MTOW,
                        'empty_weight' => $config->EmptyWeight,
                        'price' => $config->Price,
                        'ext1' => $config->Ext1,
                        'ltip' => $config->LTip,
                        'laux' => $config->LAux,
                        'lmain' => $config->LMain,
                        'center1' => $config->Center1,
                        'center2' => $config->Center2,
                        'center3' => $config->Center3,
                        'rmain' => $config->RMain,
                        'raux' => $config->RAux,
                        'rtip' => $config->RTip,
                        'ext2' => $config->Ext2,
                        'engines' => $config->Engines,
                        'engine_price' => $config->EnginePrice,
                        'model_id' => $config->ModelId,
                    ]
                );
            }
        }
        else
        {
            Log::debug($aircraftConfig['message']);
        }

        Log::debug('FBOs: Ending FBO pull');
    }
}
