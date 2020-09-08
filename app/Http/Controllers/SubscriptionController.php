<?php

namespace App\Http\Controllers;

use App\FBOFuelLevelThreshold;
use App\FBOSupplyLevelThreshold;
use App\Payment;
use App\Service;
use App\Subscription;
use App\Http\Requests\Subscriptions\NewSubscriptionRequest;
use App\SubscriptionStatus;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'My Subscriptions',
            'subscriptions' => Subscription::own()->active()->get(),
            'subscriptionsPast' => Subscription::own()->inactive()->get(),
        ];

        return view('pages.subscriptions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function create(Service $service)
    {
        $data = [
            'pageTitle' => 'Add Subscription',
            'selectedService' => $service->is_active ? $service : abort(404),
        ];

        return view('pages.subscriptions.add', $data);
    }

    public function store(NewSubscriptionRequest $request)
    {
        // Store subscription
        $subscription = Subscription::updateOrCreate(
            ['user_id' => \Auth::id(),
            'group_id' => $request->group_id,
            'service_id' => $request->service_id,
            'monthly_cost' => $request->payment_amount,
            'next_due_date' => Carbon::parse(now())->addDays(30)->format('Y-m-d')],
            ['status' => 1]
        );

        // Store payment
        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'user_id' => \Auth::id(),
            'amount' => $request->payment_amount,
            'payer' => $request->pmt_rec_from,
        ]);

        // Create Threshold level record if a fbo fuel monitor subscription
        if ($request->service_id == 12)
        {
            FBOFuelLevelThreshold::create([
                'subscription_id' => $subscription->id,
                'jeta_threshold' => 3000,
                'll_threshold' => 3000
            ]);
        }

        // Create Threshold level record if a fbo fuel monitor subscription
        if ($request->service_id == 13)
        {
            FBOSupplyLevelThreshold::create([
                'subscription_id' => $subscription->id,
                'supply_threshold' => 30
            ]);
        }

        // Assign role or verify roles job
        /*$exists = DB::table('role_user')
            ->select('role_id')
            ->where([
                ['user_id', '=', \Auth::id()],
                ['role_id', '=', $subscription->service->role_required]
            ])
            ->exists();*/

        //!$exists ? \Auth::user()->roles()->attach($subscription->service->role_required) : null;

        if ($subscription->wasChanged())
        {
            return redirect()->route('subscriptions.index')->with('success_message', 'Previous subscription found! Reactivating service.');
        }
        else
        {
            return redirect()->route('subscriptions.index')->with('success_message', 'Subscription started! Please allow some time for your data to be retrieved from the FSE servers. For older groups this can take some time.');
        }

        return back()->with('fail_message', 'Something went wrong!');
    }

    public function list()
    {
        $data = [
            'pageTitle' => 'Services',
            'fbo' => Service::fbo()->get(),
            'flight' => Service::flight()->get(),
            'combo' => Service::combo()->get(),
            'aircraft' => Service::aircraft()->get(),
            'monitor' => Service::monitor()->get(),
        ];

        return view('pages.subscriptions.list', $data);
    }

    public function cancel(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $data = [
            'pageTitle' => 'Cancellation Confirmation',
            'subscription' => $subscription,
        ];

        return view('pages.subscriptions.cancel', $data);
    }

    public function doCancel(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $status = SubscriptionStatus::where('string', 'Cancelled')->first();

        $subscription->status = $status->id;
        $subscription->save();

        // Remove role or verify roles job
        //\Auth::user()->roles()->detach($subscription->service->role_required);

        return redirect()->route('subscriptions.index')->with('success_message', 'Subscription cancelled!');
    }
}
