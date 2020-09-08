<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Subscription extends Model
{
    use LogsActivity;

    protected $guarded = ['created_at', 'updated_at', 'id'];
    protected static $logAttributes = ['*'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function stat()
    {
        return $this->belongsTo(SubscriptionStatus::class,'status','id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function additionalAccess()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function fuelThreshold()
    {
        return $this->hasOne(FBOFuelLevelThreshold::class);
    }

    public function supplyThreshold()
    {
        return $this->hasOne(FBOSupplyLevelThreshold::class);
    }

    public function resupplyParams()
    {
        return $this->hasOne(AutoResupplyParams::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [1, 4]);
    }

    public function scopeInactive($query)
    {
        return $query->whereIn('status', [2, 3]);
    }

    public function scopeOwn($query)
    {
        return $query->where('user_id', '=', \Auth::id());
    }
}
