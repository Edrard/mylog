<?php
namespace edrard\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Handlers
{
    private static $stdout = [];

    public static function stdout(){
        if(static::$stdout != []){
            return static::$stdout;
        }
        static::$stdout = [
            new StreamHandler('php://stdout', Logger::INFO,false),
            new StreamHandler('php://stdout', Logger::CRITICAL,false),
            new StreamHandler('php://stdout', Logger::WARNING,false),
            new StreamHandler('php://stdout', Logger::ERROR,false),
            new StreamHandler('php://stdout', Logger::DEBUG,false)
        ];
        return static::$stdout;
    }


}