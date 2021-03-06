<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    public $table = 'Userinfo';

    public function scopeGetUserId($query, $id)
    {
        return $query->select('userid')->where('badgeNumber', $id);
    }

    public function scopeSelectLog($query)
    {
        return $query->select('userinfo.userid', 'Badgenumber', 'checktime', 'checktype', 'name')->distinct()->from('USERINFO');
    }

    public function scopeJoinCol($query)
    {
        return $query->join('checkinout', 'userinfo.userid', '=', 'checkinout.userid');
    }

    public function scopeHrisId($query, $id)
    {
        return $query->where('checkinout.userid', $id);
    }

    public function scopeOrderDate($query)
    {
        return $query->orderBy('checktime','ASC');
    }

    public function scopeCompareDate($query, $date1, $date2)
    {
        return $query->whereRaw("checktime >= ? AND checktime <= ?", array($date1." 00:00:00.000", $date2." 23:59:59.000"));
    }

}
