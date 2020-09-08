<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Watson\Rememberable\Rememberable;

class FSEPayment extends Model
{
    use SerializesModels;

    protected $table = 'fse_payments';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function fseaircraft()
    {
        return $this->belongsTo(FSEAircraft::class, 'aircraft','registration');
    }

    /*
     * Scopes
     */
    public function scopeJetA($query)
    {
        return $query->where('reason', 'like', '%refuelling%')->where('reason', 'like', '%jeta%');
    }

    public function scopeLL($query)
    {
        return $query->where('reason', 'like', '%refuelling%')->where('reason', 'like', '%100ll%');
    }

    public function scopeMonth($query, $monthyear)
    {
        return $query->whereBetween('date', [$monthyear . '-01 00:00:00', $monthyear . '-31 23:59:59']);
    }

    public function scopeGroup($query, $group_id)
    {
        return $query->where('group_id', '=', $group_id);
    }

    public function scopePaidTo($query, $paidto)
    {
        return $query->where('p_to', '=', $paidto);
    }

    public function scopeFBO($query, $fbo)
    {
        return $query->where('fbo', '=', $fbo);
    }

    public function scopeLikeReason($query, $reason)
    {
        return $query->where('reason', 'like', '%' . $reason . '%');
    }

    public function scopeEqualReason($query, $reason)
    {
        return $query->where('reason', '=', $reason);
    }

    public function scopeLastSixMonths($query)
    {
        $now = Carbon::now();
        $six = $now->copy()->modify('-6 months');
        return $query->whereBetween('date', [$six->format('Y-m') . '-01 00:00:00', $now->format('Y-m') . '-31 23:59:59']);
    }
}
