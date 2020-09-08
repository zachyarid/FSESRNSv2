<?php

namespace App\Jobs;

use App\FSEFbo;
use App\Group;
use App\Libraries\FSEData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetFSEFbos implements ShouldQueue
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
        Log::debug('FBOs: Starting FBO pull');

        $groups = Group::where('cron_disable', 0)->get();
        $fseData = new FSEData();

        foreach ($groups as $g)
        {
            sleep(1);

            $fbos = $fseData->getFbos($g->access_key);

            $this->removeFbos($g->id);

            if ($fbos['success'])
            {
                $all = $fbos['message'];

                $count = 0;
                foreach ($all as $fbo) {
                    FSEFbo::create([
                        'id' => $fbo->FboId,
                        'group_id' => $g->id,
                        'status' => $fbo->Status,
                        'airport' => $fbo->Airport,
                        'name' => $fbo->Name,
                        'owner' => $fbo->Owner,
                        'icao' => $fbo->Icao,
                        'location' => $fbo->Location,
                        'lots' => $fbo->Lots,
                        'repair_shop' => $fbo->RepairShop,
                        'gates' => $fbo->Gates,
                        'gates_rented' => $fbo->GatesRented,
                        'fuel_100ll' => $fbo->Fuel100LL,
                        'fuel_jeta' => $fbo->FuelJetA,
                        'building_materials' => $fbo->BuildingMaterials,
                        'supplies' => $fbo->Supplies,
                        'supplies_per_day' => $fbo->SuppliesPerDay,
                        'supplied_days' => $fbo->SuppliedDays,
                        'sell_price' => $fbo->SellPrice
                    ]);
                    $count++;
                }

                Log::debug('FBOs: Inserted ' . $count . ' FBOs for group ' . $g->name);
            }
            else
            {
                Log::debug($fbos['message']);
            }

            sleep(1);
        }

        Log::debug('FBOs: Ending FBO pull');
    }

    public function removeFbos($group_id)
    {
        return DB::table('fse_fbos')
            ->where('group_id', $group_id)
            ->delete();
    }
}
