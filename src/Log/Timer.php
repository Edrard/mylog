<?php
namespace edrard\Log;


class Timer
{
    protected static $time_start = [];
    protected static $time_end = [];
    protected static $execution_time = [];

    public static function startTime($type = 'global')
    {
        static::$time_start[$type] = microtime(true);
    }
    public static function endTime($type = 'global', $round = 1)
    {
        static::$time_end[$type] = microtime(true);
        static::$execution_time[$type] = static::$time_end[$type] - static::$time_start[$type];
        if($round != 0){
            $koeff = pow(10, $round);
            static::$execution_time[$type] = round(static::$execution_time[$type] * $koeff)/$koeff;
        }
    }
    public static function getTime($type = 'global', $round = 1)
    {
       if(!isset(static::$execution_time[$type]) ||  !static::$execution_time[$type]){
            static::endTime($type,$round);
        }
        return isset(static::$execution_time[$type]) ? static::$execution_time[$type] : "" ;
    }
}
