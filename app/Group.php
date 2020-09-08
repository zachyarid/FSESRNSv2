<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Group extends Model
{
    use LogsActivity;

    protected $guarded = ['created_at', 'updated_at', 'id', 'cron_disable'];
    protected static $logAttributes = ['*'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function fseaircraft()
    {
        return $this->hasMany(FSEAircraft::class);
    }

    public function fsefbos()
    {
        return $this->hasMany(FSEFbo::class);
    }

    public function fseflightlogs()
    {
        return $this->hasMany(FSEFlightLog::class);
    }

    public function fseflightloggroupaircrft()
    {
        return $this->hasMany(FSEFlightLogAircraft::class);
    }

    public function fsepayments()
    {
        return $this->hasMany(FSEPayment::class);
    }
}
