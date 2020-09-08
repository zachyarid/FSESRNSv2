<?php

namespace App\Jobs;

use App\FSEPayment;
use App\Group;
use App\Libraries\FSEData;
use App\MonthPull;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetFSEPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 480;

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
        ini_set("memory_limit", "-1");

        Log::debug('Payments: Starting Payment pull');

        $groups = Group::where('cron_disable', 0)->get();
        $fseData = new FSEData();

        foreach ($groups as $g)
        {
            Log::debug('Payments: Group ' . $g->name);
            $didMonthPull = false;
            $monthpull = $this->getLastMonthPullSet($g->id);

            // determine month or by ID
            if ($monthpull['message'] > 0 || $monthpull['rows'] < 6) {
                $mp = $this->getMPDate($g->id);
                $payments = $fseData->getPaymentsMonth($g->access_key, $mp['month'], $mp['year']);
                $didMonthPull = true;
            } elseif ($monthpull['message'] == 0) {
                $maxID = $this->getMaxID($g->id);
                $payments = $fseData->getPayments($g->access_key, $maxID);
            } else {
                $mp = $this->getMPDate($g->id);
                $payments = $fseData->getPaymentsMonth($g->access_key, $mp['month'], $mp['year']);
                $didMonthPull = true;
            }

            $didMonthPull ? Log::debug('Payments: Month Pull enabled') : null;
            $didMonthPull ? Log::debug('Payments: ' . $mp['month'] . '-' . $mp['year']) : null;

            if ($payments['mp_only'])
            {
                if ($didMonthPull)
                {
                    // Insert Month Pull Record
                    MonthPull::create([
                        'group_id' => $g->id,
                        'type' => 'P',
                        'month_pulled' => $mp['year'] . '-' . $mp['month'] . '-01',
                        'completed' => Carbon::now(),
                        'records_inserted' => 0
                    ]);
                }
            }
            else if ($payments['success'])
            {
                Log::debug("Payments: Inserting payments");
                $count = 0;
                foreach ($payments['message']->Payment as $payment)
                {
                    FSEPayment::updateOrcreate(
                        ['fse_id' => $payment->Id, 'group_id' => $g->id],
                        ['fse_id' => $payment->Id,
                        'group_id' => $g->id,
                        'date' => $payment->Date,
                        'p_to' => $payment->To,
                        'p_from' => $payment->From,
                        'amount' => $payment->Amount,
                        'reason' => $payment->Reason,
                        'fbo' => substr($payment->Fbo,0,250),
                        'location' => $payment->Location,
                        'aircraft' => $payment->Aircraft,
                        'comment' => $payment->Comment
                    ]);

                    //Log::debug("Payments: Inserting ID => " . $payment->Id);
                    $count++;
                }

                Log::debug('Payments: Inserted ' . $count . ' payments for group ' . $g->name);

                if ($didMonthPull)
                {
                    // Insert Month Pull Record
                    MonthPull::create([
                        'group_id' => $g->id,
                        'type' => 'P',
                        'month_pulled' => $mp['year'] . '-' . $mp['month'] . '-01',
                        'completed' => Carbon::now(),
                        'records_inserted' => $count
                    ]);
                }
            }
            else if ($payments['mp_only'])
            {
                if ($didMonthPull)
                {
                    // Insert Month Pull Record
                    MonthPull::create([
                        'group_id' => $g->id,
                        'type' => 'P',
                        'month_pulled' => $mp['year'] . '-' . $mp['month'] . '-01',
                        'completed' => Carbon::now(),
                        'records_inserted' => 0
                    ]);
                }
            }
            else
            {
                Log::debug('Payments: ' . $payments['message']);
            }

            sleep(2);
        }

        Log::debug('Payments: Ending Payment pull');
    }

    public function getMaxID($groupID)
    {
        return DB::table('fse_payments')
            ->where('group_id', $groupID)
            ->max('fse_id') ?? 0;
    }

    public function getMPDate($group_id)
    {
        // Get previous month pull from month_pull
        $result = DB::table('month_pull')
            ->where([
                ['group_id', '=', $group_id],
                ['type', '=', 'P'],
            ])
            ->limit(1)
            ->orderByDesc('id')
            ->first();

        // If no rows exist, no single month has been pulled.
        // Start with the current month
        if ($result) {
            // If a row does exist, decrement it to pull the previous month
            $now = Carbon::parse($result->month_pulled);
            $now->modify(' -1 month');
        } else {
            $now = Carbon::now();
        }

        return array('month' => $now->format('m'), 'year' => $now->format('Y'));
    }

    public function getLastMonthPullSet($group_id)
    {
        $result = DB::table('month_pull')
            ->select('records_inserted')
            ->where([
                ['group_id', '=', $group_id],
                ['type', '=', 'P'],
            ])
            ->limit(6)
            ->orderByDesc('id')
            ->get();

        $total = 0;
        foreach ($result as $row) {
            $total += $row->records_inserted;
        }

        if ($result) {
            return array('success' => true, 'message' => $total, 'rows' => count($result));
        } else {
            return array('success' => false, 'message' => $this->db->error());
        }

    }
}
