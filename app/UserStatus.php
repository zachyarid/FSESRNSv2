<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $table = 'user_status';

    public function users()
    {
        return $this->hasMany(User::class, 'status', 'id');
    }
}
