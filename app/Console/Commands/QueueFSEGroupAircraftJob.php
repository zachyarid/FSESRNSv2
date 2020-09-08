<?php

namespace App\Console\Commands;

use App\Jobs\GetFSEGroupAircraft;
use Illuminate\Console\Command;

class QueueFSEGroupAircraftJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:aircraft';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to fetch FSE Group Aircraft';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new GetFSEGroupAircraft())->onQueue('high');
    }
}
