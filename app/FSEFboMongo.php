<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class FSEFboMongo extends Model
{
    protected $collection = 'fse_fbos';
    protected $primaryKey = 'id';
    protected $connection = 'mongodb';
}
