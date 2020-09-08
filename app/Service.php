<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use LogsActivity;

    protected $guarded = ['created_at', 'updated_at', 'id'];
    protected static $logAttributes = ['*'];

    public function scopeFbo($query)
    {
        return $query->where('type', '=', 'fbo')->where('is_active', '=', 1)->orderBy('base_cost');
    }

    public function scopeFlight($query)
    {
        return $query->where('type', '=', 'flight')->where('is_active', '=', 1)->orderBy('base_cost');
    }

    public function scopeAircraft($query)
    {
        return $query->where('type', '=', 'aircraft')->where('is_active', '=', 1)->orderBy('base_cost');
    }

    public function scopeCombo($query)
    {
        return $query->where('type', '=', 'combo')->where('is_active', '=', 1)->orderBy('base_cost');
    }

    public function scopeMonitor($query)
    {
        return $query->where('type', '=', 'monitor')->where('is_active', '=', 1)->orderBy('base_cost');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
