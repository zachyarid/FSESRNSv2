<?php

namespace App\Http\Controllers;

use App\FSEAircraft;
use App\FSEAssignment;
use App\FSEFbo;
use App\FSEPayment;
use App\Jobs\GenerateMassFBOPandLReport;
use App\Libraries\FSEData;
use App\Libraries\ReportHelper;
use App\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function view(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $reportHelper = new ReportHelper($subscription->id);

        switch ($subscription->service_id)
        {
            // Group flight tracking
            case 1:
                $reportData = $reportHelper->getFlightReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' Monthly Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'historic' => $reportHelper->formMorrisBarDataProf($reportHelper->getHistoricGroupProfit()),
                ];

                return view('pages.reports.mockup', $data);
                break;

            // Group FBO tracking
            case 2:
                $reportData = $reportHelper->getFBOReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' FBO Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'historic_gcf' => $reportHelper->formMorrisBarDataGCF($reportHelper->getHistoricGCFs()),
                    //'historic1' => $reportHelper,
                ];

                return view('pages.reports.fbo', $data);
                break;

            // Combo tracking
            case 3:
                $reportData = $reportHelper->getFlightReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' Monthly Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'flbyprof' => $reportHelper->getFlightsByProf(),
                    'historic' => $reportHelper->formMorrisBarDataProf($reportHelper->getHistoricGroupProfit()),
                ];

                return view('pages.reports.mockup', $data);
                break;

            // 1 Month Flight trial
            case 4:
                $reportData = $reportHelper->getFlightReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' Monthly Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'flbyprof' => $reportHelper->getFlightsByProf(),
                    'historic' => $reportHelper->formMorrisBarDataProf($reportHelper->getHistoricGroupProfit()),
                ];

                return view('pages.reports.mockup', $data);
                break;

            // on the house tracking
            case 5:
                break;

            // Personal flight tracking
            case 6:
                $data = [
                    'pageTitle' => $subscription->group->name . ' Monthly Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportHelper->getPersonalFlightReportData(),
                    'historic' => $reportHelper->getHistoricPilotProfit(),
                ];

                return view('pages.reports.personal', $data);
                break;

            // 1 month fbo trial
            case 7:
                $reportData = $reportHelper->getFBOReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' FBO Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'historic_gcf' => $reportHelper->formMorrisBarDataGCF($reportHelper->getHistoricGCFs()),
                ];

                return view('pages.reports.fbo', $data);
                break;

            // rental fleet tracker
            case 8:
                $reportData = '';

                $data = [
                    'pageTitle' => $subscription->group->name . ' - Rental Fleet Tracker',
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                ];



                return view('pages.reports.rental', $data);
                break;

            // brokered lease
            case 9:
                break;

            // brokered financing
            case 10:
                break;

            // personal fbo tracking
            case 11:
                $reportData = $reportHelper->getFBOReportData();
                $data = [
                    'pageTitle' => $subscription->group->name . ' FBO Report - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
                    'subscription' => $subscription,
                    'reportData' => $reportData,
                    'historic_gcf' => $reportHelper->formMorrisBarDataGCF($reportHelper->getHistoricGCFs()),
                ];

                return view('pages.reports.fbo', $data);
                break;

            default:
                abort(404);
        }
    }

    public function test(Subscription $subscription)
    {
        $subscriptions = Subscription::where('service_id', 12)->where('status', 1)->with('group')->get();


        foreach ($subscriptions as $subscription)
        {
            $fbos = $subscription->group->fsefbos;

            if ($subscription->id == 13)
            {
                dd($fbos);
            }
        }

        return $subscriptions;
    }

    public function pAndL(Subscription $subscription, FseFbo $fboid, $num)
    {
        $reportHelper = new ReportHelper($subscription->id);

        $res = $reportHelper->lastNumMonthsForOneFBO($fboid, $num);

        return response()->json($res);
    }

    public function viewAircraft(Subscription $subscription, FSEAircraft $aircraft)
    {
        $this->authorize('view', $subscription);

        if ($subscription->service->type == 'flight' || $subscription->service->type == 'combo')
        {
            dd($aircraft);
        }
        else
        {
            abort(403);
        }
    }

    public function viewPilot(Subscription $subscription, $pilot)
    {
        $this->authorize('view', $subscription);

        $reportHelper = new ReportHelper($subscription->id);

        $data = [
            'pageTitle' => $subscription->group->name . ' Monthly Report for pilot ' . $pilot . ' - '.\Carbon\Carbon::parse(session()->get('monthyear'))->format('M Y'),
            'subscription' => $subscription,
            'aircraft' => $reportHelper->getFlightsByAircraft($pilot)['message'],
            'historic' => $reportHelper->formMorrisBarDataProf($reportHelper->getHistoricGroupProfit($pilot)),
            'fuelpmtpay' => $reportHelper->getFuelPaymentsByPayer($pilot)['message'],
            'll' => $reportHelper->getAll100LLPayments()['message'],
            'jeta' => $reportHelper->getAllJetAPayments()['message'],
            'acfbofuel' => $reportHelper->getRefuelRevByAircraft($pilot),
        ];

        return view('pages.reports.pilot', $data);
    }

    public function requestFBOPandL(Subscription $subscription)
    {
        // make job
        dispatch(new GenerateMassFBOPandLReport($subscription));

        // reset request time
        $now = Carbon::now();
        $subscription->last_pandlrequest = $now->format('Y-m-d H:i:s');
        $subscription->save();

        // return
        return response()->json([
            'success' => true,
            'message' => 'An FBO P/L report request has been sent and will be emailed to the email address we have on file as soon as it is generated.',
        ]);
    }

}
