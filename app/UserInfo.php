<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserInfo extends Model
{
    public $table = 'Userinfo';

    public function scopeSelectLogIn($query)
    {
        return $query->select('userinfo.userid', 'Badgenumber', 'checktime', 'checktype', 'name');
    }

    public function scopeSelectLogOut($query)
    {
        return $query->select('checktime', 'checktype');
    }

    public function scopeJoinCol($query)
    {
        return $query->join('checkinout', 'userinfo.userid', '=', 'checkinout.userid');
    }

    public function scopeHrisId($query, $id)
    {
        return $query->where('checkinout.userid', $id);
    }

    public function scopeTimeType($query, $id)
    {
        return $query->where('checktype', $id);
    }

    public function scopeOrderDate($query)
    {
        return $query->orderBy('checktime','ASC');
    }

    public function scopeCompareDate($query, $date1, $date2)
    {
        return $query->whereBetween(DB::raw('date'),array($date1,$date2));
    }

}
