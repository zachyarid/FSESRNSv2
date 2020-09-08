<?php

namespace App\Jobs;

use App\Group;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class GetFSEFlightLogGroupAircraft implements ShouldQueue
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
        $groups = Group::where('cron_disable', 0)->get();

        foreach ($groups as $g)
        {
            DB::table('datafeed_queue')
                ->insert([
                    'group_id' => $g->id,
                    'access_key' => $g->access_key,
                    'pull_type' => 3,
                    'created_at' => Carbon::parse(now())->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse(now())->format('Y-m-d H:i:s'),
                ]);
        }
    }
}
