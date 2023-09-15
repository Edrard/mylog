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
    public static function endTime($type = 'global')
    {
        static::$time_end[$type] = microtime(true);
        static::$execution_time[$type] = static::$time_end[$type] - static::$time_start[$type];
    }
    public static function getTime($type = 'global')
    {
       if(!isset(static::$execution_time[$type]) ||  !static::$execution_time[$type]){
            static::endTime($type);
        }
        return isset(static::$execution_time[$type]) ? static::$execution_time[$type] : "" ;
    }
}
