<?php

class AclDbHandler
{
    static public function getDriver($connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }

        return config("database.connections.{$connection}.driver");
    }

    static public function getCurrentTimestamp($connection = null)
    {
        $tsPrecision = config('acl.timestamp_precision');
        
        $currentTimestamp = 'CURRENT_TIMESTAMP';

        if (!empty($tsPrecision) and static::getDriver($connection) != 'sqlite') {
            $currentTimestamp .= "({$tsPrecision})";
        }

        return $currentTimestamp;
    }
}
