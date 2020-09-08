<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoResupplyParams extends Model
{
    protected $table = 'auto_resupply_params';

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
