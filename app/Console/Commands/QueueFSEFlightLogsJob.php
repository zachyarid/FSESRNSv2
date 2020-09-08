<?php

namespace App\Console\Commands;

use App\Jobs\GetFSEFlightLogs;
use Illuminate\Console\Command;

class QueueFSEFlightLogsJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:flightlogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to fetch FSE Flight Logs';

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
        dispatch(new GetFSEFlightLogs())->onQueue('high');
    }
}
