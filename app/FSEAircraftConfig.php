<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FSEAircraftConfig extends Model
{
    protected $table = 'aircraft_config';
    protected $primaryKey = 'make_model';

    protected $guarded = ['id'];
}
