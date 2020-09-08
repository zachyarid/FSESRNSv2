<?php

namespace App\Console\Commands;

use App\Jobs\GetFSEPayments;
use Illuminate\Console\Command;

class QueueFSEPaymentsJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to fetch FSE Payments';

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
        dispatch(new GetFSEPayments())->onQueue('high');
    }
}
