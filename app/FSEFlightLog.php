<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Watson\Rememberable\Rememberable;

class FSEFlightLog extends Model
{
    use SerializesModels;

    protected $table = 'fse_flightlogs';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function pilotid()
    {
        return $this->hasOne(FSEIDUsername::class, 'username', 'pilot');
    }

    public function aircraftcfg()
    {
        return $this->belongsTo(FSEAircraftConfig::class, 'make_model', 'make_model');
    }

    public function scopeMonth($query, $monthyear)
    {
        return $query->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59']);
    }

    public function scopeGroup($query, $group_id)
    {
        return $query->where('group_id', '=', $group_id);
    }

    public function scopeMonthWindow($query, $early, $late)
    {
        return $query->whereBetween('date', [$early . '-01 00:00:00', $late . '-31 23:59:59']);
    }
}
