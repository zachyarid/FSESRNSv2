<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerializedMonthlyReport extends Model
{
    protected $table = 'serialized_monthly_reports';

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
