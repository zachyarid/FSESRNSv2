<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable;
    use LogsActivity;

    protected static $logAttributes = ['*'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname', 'lname', 'username', 'personal_key', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->hasMany(Group::class)->where('is_active', '=', 1);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->where('status', '=', 1);
    }

    public function reportSubscriptions()
    {
        //return $this->hasMany(Subscription::class)->where('status', 1);
        return Subscription::whereHas('service', function ($query) {
            $query->where('render_type', 'report');
        })->where('status', 1)->where('user_id', \Auth::id())->get();
    }

    public function monitorSubscriptions()
    {
        //return $this->hasMany(Subscription::class)->where('status', 1)->where('services.render_type', 'monitor');
        return Subscription::whereHas('service', function ($query) {
            $query->where('render_type', 'monitor');
        })->where('status', 1)->where('user_id', \Auth::id())->get();
    }

    public function specialpayments()
    {
        return $this->hasMany(SpecialPayment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function stat()
    {
        return $this->belongsTo(UserStatus::class,'status','id');
    }

    public function sharedSubscriptions()
    {
        return $this->belongsToMany(Subscription::class)
            ->withPivot('granted_by')
            ->withTimestamps();
    }

    public function grantedSubscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_user', 'granted_by','subscription_id')
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
