<?php

namespace App\Http\Controllers;

use App\AutoResupplyFBO;
use App\AutoResupplyParams;
use App\FBOFuelLevelThreshold;
use App\FBOSupplyLevelThreshold;
use App\FSEFbo;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitorController extends Controller
{
    public function view(Subscription $subscription)
    {
        switch ($subscription->service_id)
        {
            case 12:
                $data = [
                    'pageTitle' => $subscription->group->name . ' - Fuel Level Monitor',
                    'subscription' => $subscription,
                    'fbos' => $this->getFuelWarningFbos($subscription),
                    'threshold' => $subscription->fuelthreshold,
                ];

                return view('pages.monitors.fbo-fuel', $data);
                break;

            case 13:
                $data = [
                    'pageTitle' => $subscription->group->name . ' - Supply Level Monitor',
                    'subscription' => $subscription,
                    'fbos' => $this->getSupplyWarningFbos($subscription),
                    'threshold' => $subscription->supplythreshold,
                ];

                return view('pages.monitors.fbo-supply', $data);

                break;

            case 14:
                $data = [
                    'pageTitle' => $subscription->group->name . ' - FBO Auto Resupply',
                    'subscription' => $subscription,
                    'allfbos' => $subscription->group->fsefbos,
                    'param' => $subscription->resupplyparams
                ];

                return view('pages.monitors.fbo-auto-resupply', $data);

                break;

            default:
                echo 'not set';
                break;
        }
    }

    private function getFuelWarningFbos(Subscription $subscription)
    {
        return DB::table('fse_fbos')
            ->where(function ($query) use ($subscription) {
                $query->where('fuel_jeta', '<=', $subscription->fuelthreshold->jeta_threshold)
                    ->orWhere('fuel_100ll', '<=', $subscription->fuelthreshold->ll_threshold);
            })
            ->where('group_id', $subscription->group_id)
            ->get();
    }

    private function getSupplyWarningFbos(Subscription $subscription)
    {
        return DB::table('fse_fbos')
            ->where('supplied_days', '<=', $subscription->supplythreshold->supply_threshold)
            ->where('group_id', $subscription->group_id)
            ->get();
    }

    public function changeFuelThresholds(Request $request)
    {
        $threshold = FBOFuelLevelThreshold::findOrFail($request->id);

        $threshold->jeta_threshold = $request->jeta;
        $threshold->ll_threshold = $request->ll;

        $threshold->save();

        return response()->json(['message' => 'Threshold changed!', 'success' => true]);
    }

    public function changeSupplyThresholds(Request $request)
    {
        $threshold = FBOSupplyLevelThreshold::findOrFail($request->id);

        $threshold->supply_threshold = $request->supply;
        $threshold->save();

        return response()->json(['message' => 'Threshold changed!', 'success' => true]);
    }

    public function changeAutoResupplyParams(Request $request)
    {
        $arp = AutoResupplyParams::findOrFail($request->id);

        $arp->resupply_days = $request->days;
        $arp->resupply_amount = $request->amount;
        $arp->save();

        return response()->json(['message' => 'Auto resupply parameters changed!', 'success' => true]);
    }

    public function saveAutoResupplyFBOs(Subscription $subscription, Request $request)
    {
        $subscription_id = $subscription->id;

        foreach ($request->selected_fbos as $fbo)
        {
            $fsefbo = FSEFbo::findOrFail($fbo);

            $arsfbo = new AutoResupplyFBO;
            $arsfbo->subscription_id = $subscription_id;
        }
    }

    public function buySupplies($amount, $account, $icao)
    {
        $options = [
            'form_params' => [
                'returnpage' => '/airport.jsp',
                'return' => 'airport.jsp',
                'event' => 'buyGoods',
                'owner' => '0',
                'type' => '2',
                'amount' => "$amount",
                'account' => $account,
                'icao' => $icao
            ]
        ];
    }
}
