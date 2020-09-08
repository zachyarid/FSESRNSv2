<?php

namespace App\Console\Commands;

use App\Libraries\FSEApi;
use App\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckForFSEIDs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:fseid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tables for missing FSEIDs';

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
        $fseApi = new FSEApi();

        $list = [];

        $fseidusername = DB::table('fseid_username')->get()->toArray();
        foreach ($fseidusername as $i)
        {
            $list[] = $i->username;
        }

        $payments = DB::table('fse_payments')->select(DB::raw('distinct p_from'))->get();

        $bar = $this->output->createProgressBar(count($payments));
        $bar->start();

        foreach ($payments as $payment)
        {


            if (!in_array($payment->p_from, $list))
            {
                $fseid = $fseApi->getFSEID($payment->p_from);

                if ($fseid !== null)
                {
                    DB::table('fseid_username')->updateOrInsert(
                        ['id' => $fseid, 'username' => $payment->p_from],
                        ['id' => $fseid, 'username' => $payment->p_from]
                    );
                }
            }


            $bar->advance();
        }

        $bar->finish();
    }
}
