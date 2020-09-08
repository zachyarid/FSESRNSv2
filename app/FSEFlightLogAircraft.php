<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FSEFlightLogAircraft extends Model
{
    protected $table = 'fse_flightlog_groupaircraft';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeMonth($query, $monthyear)
    {
        return $query->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59']);
    }

    public function scopeGroup($query, $group_id)
    {
        return $query->where('group_id', '=', $group_id);
    }
}
