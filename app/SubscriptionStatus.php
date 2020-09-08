<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionStatus extends Model
{
    protected $table = 'subscription_status';

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'status', 'id');
    }
}
