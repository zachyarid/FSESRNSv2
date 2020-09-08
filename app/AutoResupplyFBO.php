<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoResupplyFBO extends Model
{
    protected $table = 'auto_resupply_fbos';

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
