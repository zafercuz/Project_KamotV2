<?php

namespace App\Helpers;
use Config;
use DB;

class DatabaseConnection
{
    public static function setConnection($params)
    {
        config(['database.connections.onthefly' => [
            'driver' => $params->driver,
            'host' => $params->host,
            'port' => $params->port,
            'username' => $params->username,
            'password' => $params->password
        ]]);

        return DB::connection('onthefly');
    }
}