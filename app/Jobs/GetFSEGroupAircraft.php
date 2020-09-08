<?php

namespace App\Jobs;

use App\FSEAircraft;
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

class GetFSEGroupAircraft implements ShouldQueue
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
        Log::debug('Aircraft: Starting Aircraft pull');

        $groups = Group::where('cron_disable', 0)->get();
        $fseData = new FSEData();

        foreach ($groups as $g)
        {
            sleep(1);
            $aircraft = $fseData->getAircraft($g->access_key);

            $this->removeAircraft($g->id);

            if ($aircraft['success'])
            {
                $all = $aircraft['message'];
                $count = 0;
                foreach ($all as $aircraft)
                {
                    FSEAircraft::create([
                        'group_id' => $g->id,
                        'serial_number' => $aircraft->SerialNumber,
                        'make_model' => $aircraft->MakeModel,
                        'registration' => $aircraft->Registration,
                        'owner' => $aircraft->Owner,
                        'location' => $aircraft->Location,
                        'location_name' => $aircraft->LocationName,
                        'home' => $aircraft->Home,
                        'sale_price' => $aircraft->SalePrice,
                        'sell_back_price' => $aircraft->SellbackPrice,
                        'equipment' => $aircraft->Equipment,
                        'rental_dry' => $aircraft->RentalDry,
                        'rental_wet' => $aircraft->RentalWet,
                        'bonus' => $aircraft->Bonus,
                        'rental_time' => $aircraft->RentalTime,
                        'rented_by' => $aircraft->RentedBy,
                        'fuel_pct' => $aircraft->FuelPct,
                        'needs_repair' => $aircraft->NeedsRepair,
                        'airframe_time' => $aircraft->AirframeTime,
                        'engine_time' => $aircraft->EngineTime,
                        'time_last_100hr' => $aircraft->TimeLast100hr,
                        'leased_from' => $aircraft->LeasedFrom,
                        'monthly_fee' => $aircraft->MonthlyFee,
                        'fee_owed' => $aircraft->FeeOwed
                    ]);
                    $count++;
                }

                Log::debug('Aircraft: Inserted ' . $count . ' aircraft for group ' . $g->id);
            }
            else
            {
                Log::debug($aircraft['message']);
            }

            sleep(1);
        }

        Log::debug('Aircraft: Ending Aircraft pull');
    }

    public function removeAircraft($group_id)
    {
        return DB::table('fse_groupaircraft')
            ->where('group_id', $group_id)
            ->delete();
    }
}
