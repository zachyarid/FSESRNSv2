<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthPull extends Model
{
    protected $table = 'month_pull';
    public $timestamps = false;

    protected $guarded = ['id'];
}
