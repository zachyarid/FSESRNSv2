<?php

namespace App\Console\Commands;

use App\Jobs\GetFSEFbos;
use Illuminate\Console\Command;

class QueueFSEFbosJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:fbos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job to fetch FSE FBOs';

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
        dispatch(new GetFSEFbos())->onQueue('high');
    }
}
