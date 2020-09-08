<?php

namespace App\Policies;

use App\Role;
use App\Subscription;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    // Super admin access
    public function before($user)
    {
        $administrator = Role::where('name', 'Administrator')->first();

        if ($user->roles->contains($administrator->id))
        {
            return true;
        }
    }

    public function view(User $user, Subscription $subscription)
    {
        $allowed = [];

        // All Additional user access
        $aua = DB::table('subscription_user')
            ->select('user_id')
            ->where('subscription_id', $subscription->id)
            ->get();

        foreach ($aua as $u)
        {
            $allowed[] = $u->user_id;
        }

        // Subscription owner
        $allowed[] = $subscription->user_id;

        return in_array($user->id, $allowed);
    }

    public function create(User $user)
    {
        // potential for subscription status check here
    }

    public function update(User $user, Subscription $subscription)
    {
        return $user->id === $subscription->user_id;
    }

    public function delete(User $user, Subscription $subscription)
    {
        return $user->id === $subscription->user_id;
    }
}
