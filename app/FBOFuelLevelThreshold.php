<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FBOFuelLevelThreshold extends Model
{
    protected $table = 'fbo_fuel_level_thresholds';

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
