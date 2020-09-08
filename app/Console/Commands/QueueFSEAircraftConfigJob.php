<?php

namespace App\Console\Commands;

use App\Jobs\GetFSEAircraftConfig;
use Illuminate\Console\Command;

class QueueFSEAircraftConfigJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:aircraftconfig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to update FSE Aircraft Config';

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
        dispatch(new GetFSEAircraftConfig())->onQueue('high');
    }
}
