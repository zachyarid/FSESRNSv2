<?php

namespace App\Console\Commands;

use App\Jobs\GetAllInAssignments;
use Illuminate\Console\Command;

class QueueAllInAssignmentsJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:allins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to fetch All-In assignments';

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
        dispatch(new GetAllInAssignments())->onQueue('high');
    }
}
