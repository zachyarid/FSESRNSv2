<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Subscriptions\AdminNewSubscriptionRequest;
use App\Http\Requests\Users\AdminNewUserRequest;
use App\Payment;
use App\Service;
use App\Subscription;
use App\SubscriptionStatus;
use App\User;
use App\UserStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {

    }

    public function groupsIndex()
    {
        $data = [
            'pageTitle' => 'Admin - View Groups',
            'groups' => Group::all()
        ];

        return view('pages.admin.groups', $data);
    }

    public function groupsStore()
    {
        $data = [
            'pageTitle' => 'Admin - Add Group',
        ];

        return view('pages.admin.add.group', $data);
    }



    public function subscriptionsIndex()
    {
        $data = [
            'pageTitle' => 'Admin - View Subscriptions',
            'subscriptions' => Subscription::all(),
        ];

        return view('pages.admin.subscriptions', $data);
    }

    public function subscriptionsManage(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $data = [
            'pageTitle' => 'Admin - Manage Subscription',
            'subscription' => $subscription
        ];

        return view('pages.admin.subscription-manage', $data);
    }

    public function subscriptionsCancel(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $status = SubscriptionStatus::where('string', 'Cancelled')->first();

        $subscription->status = $status->id;
        $subscription->save();

        // Remove role or verify roles job
        //$subscription->user->roles()->detach($subscription->service->role_required);

        return redirect()->route('admin.subscriptions')->with('success_message', 'Subscription cancelled!');
    }

    public function subscriptionsCreate()
    {
        $data = [
            'pageTitle' => 'Admin - Add Subscription',
            'services' => Service::all(),
            'groups' => Group::active()->get(),
        ];

        return view('pages.admin.add.subscription', $data);
    }

    public function subscriptionsStore(AdminNewSubscriptionRequest $request)
    {
        $group = Group::find($request->group_id);
        $service = Service::find($request->service_id);

        // add subscription
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $group->user_id,
            'group_id' => $group->id,
            'service_id' => $service->id,
            'monthly_cost' => $request->monthly_rate ?? $service->base_cost,
            'next_due_date' => Carbon::parse(now())->addDays(30)->format('Y-m-d')],
            ['status' => 1]
        );

        // add roles
        //$group->user->roles()->attach($subscription->service->role_required);

        // redirect
        return redirect()->route('admin.subscriptions')->with('success_message', 'Subscription added!');
    }

    public function subscriptionsReactivate(Subscription $subscription)
    {
        $status = SubscriptionStatus::where('string', 'Active')->first();

        $subscription->status = $status->id;
        $subscription->save();

        // Remove role or verify roles job
        //$subscription->user->roles()->attach($subscription->service->role_required);

        return redirect()->route('admin.subscriptions')->with('success_message', 'Subscription reactivated!');
    }




    public function paymentsIndex()
    {
        $data = [
            'pageTitle' => 'Admin - View Payments',
            'payments' => Payment::all(),
        ];

        return view('pages.admin.payments', $data);
    }

    public function paymentsConfirm(Payment $payment)
    {
        $payment->status = 'CONFIRMED';
        $payment->save();

        return redirect()->route('admin.payments')->with('success_message', 'Payment confirmed.');
    }




    public function usersIndex()
    {
        $data = [
            'pageTitle' => 'Admin - View Users',
            'users' => User::all(),
        ];

        return view('pages.admin.users', $data);
    }

    public function usersStore()
    {
        $data = [
            'pageTitle' => 'Admin - Add User',
        ];

        return view('pages.admin.add.user', $data);
    }

    public function userStore(AdminNewUserRequest $request)
    {
        $password = str_random(8);
        $hashed_random_password = Hash::make($password);

        $user = new User();

        $user->fname = $request->first_name;
        $user->lname = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->personal_key = $request->personal_ak;
        $user->status = 1;
        $user->password = $hashed_random_password;

        $user->save();

        // email user password but for now display it to the admin
        return back()->with('success_message', 'User created. Temporary password: ' . $password);
    }

    public function usersView(User $user)
    {
        $data = [
            'pageTitle' => 'Admin - View User',
            'user' => $user,
            'granted' => $user->sharedsubscriptions,
        ];

        //dd($user->sharedSubscriptions);

        return view('pages.admin.user-view', $data);
    }

    public function usersActive(User $user)
    {
        $status = UserStatus::where('string', 'Active')->first();

        $user->status = $status->id;
        $user->save();

        return redirect()->route('admin.users')->with('success_message', 'User status set to active.');
    }

    public function usersInactive(User $user)
    {
        $status = UserStatus::where('string', 'Inactive')->first();

        $user->status = $status->id;
        $user->save();

        return redirect()->route('admin.users')->with('success_message', 'User status set to inactive.');
    }

    public function usersBan(User $user)
    {
        $status = UserStatus::where('string', 'Banned')->first();

        $user->status = $status->id;
        $user->save();

        return redirect()->route('admin.users')->with('success_message', 'User status set to banned.');
    }
}
