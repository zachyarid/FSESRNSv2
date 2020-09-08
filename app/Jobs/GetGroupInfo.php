<?php

namespace App\Jobs;

use App\Libraries\FSEData;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GetGroupInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $groupID;
    public $tries = 5;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($groupID)
    {
        $this->groupID = $groupID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fseData = new FSEData();

        $fseData->getQueuedGroupData($this->groupID);
    }
}
