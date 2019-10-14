<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckInOut extends Model
{
    protected $connection = 'sqlsrv';
    public $table = 'CheckInOut';
    
    
}
