<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class FSEAircraft extends Model
{
    use SerializesModels;

    protected $table = 'fse_groupaircraft';
    protected $primaryKey = 'serial_number';

    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function config()
    {
        return $this->hasOne(FSEAircraftConfig::class, 'make_model', 'make_model');
    }
}
