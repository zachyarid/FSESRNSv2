<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FBOSupplyLevelThreshold extends Model
{
    protected $table = 'fbo_supply_level_thresholds';

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
