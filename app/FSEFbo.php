<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class FSEFbo extends Model
{
    use SerializesModels;

    protected $table = 'fse_fbos';
    public $timestamps = false;

    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeLimitGroup($query, $group_id)
    {
        return $query->where('group_id', '=', $group_id);
    }

    public function scopeJetAFuelThreshold($query, $threshold)
    {
        return $query->where('fuel_jeta', '<=', $threshold);
    }

    public function scopeLLFuelThreshold($query, $threshold)
    {
        return $query->where('fuel_100ll', '<=', $threshold);
    }
}
