<?php

namespace edrard\Log;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;
use edrard\Log\Timer;

class MyLog
{
    protected static $log = array();
    protected static $status = true; // if True, then ON, if False, then OFF
    /**
    * Numeric presset type of logs 0 - info, 1 - warning, 2 - error, 3 - critical
    *
    * @var mixed
    */
    protected static $array_type_base = array('info','warning','error','critical');
    protected static $file_types_base = array('debug' => 'debug.log','info' => 'info.log','warning' => 'error.log','error' => 'error.log','critical' => 'error.log');
    protected static $config = array();

    public static function init($path = 'logs', $ch = 'log', array $handlers = array(), $re_enable = false, $maxfiles = 60)
    {
        Timer::startTime();
        if (!isset(static::$log[$ch]) || $re_enable !== false) {
            static::$log[$ch] = new Logger($ch);
            static::$config[$ch]['type'] = static::$array_type_base;
            if ($re_enable === false) {
                static::$log[$ch]->pushHandler(new RotatingFileHandler($path.'/'.static::$file_types_base['debug'], $maxfiles, Logger::DEBUG, false));
                static::$log[$ch]->pushHandler(new RotatingFileHandler($path.'/'.static::$file_types_base['info'], $maxfiles, Logger::INFO, false));
                static::$log[$ch]->pushHandler(new RotatingFileHandler($path.'/'.static::$file_types_base['warning'], $maxfiles, Logger::WARNING, false));
                static::$log[$ch]->pushHandler(new RotatingFileHandler($path.'/'.static::$file_types_base['error'], $maxfiles, Logger::ERROR, false));
                static::$log[$ch]->pushHandler(new RotatingFileHandler($path.'/'.static::$file_types_base['critical'], $maxfiles, Logger::CRITICAL, false));
                static::$config[$ch]['types'] = static::$file_types_base;
                static::$config[$ch]['path'] = $path;
                static::$config[$ch]['maxfiles'] = $maxfiles;
                static::$config[$ch]['date'] = date('Y-m-d');
            }
            foreach ($handlers as $handler) {
                if ($handel instanceof HandlerInterface) {
                    static::$log[$ch]->pushHandler($handler);
                    static::$config[$ch]['handler'][] = $handler;
                }
            }
        }
    }
    public static function getLogConfig($id)
    {
        return static::$config[$id];
    }
    public static function changeLogFilesBase(array $file_types)
    {
        static::$file_types_base = $file_types;
    }
    public static function changeType($array_type = array('info','warning','error','critical'), $ch = 'log')
    {
        static::$config[$ch]['type'] = $array_type;
    }
    public static function changeStatus($status = true)
    {
        static::$status = $status;
    }
    public static function status()
    {
        return static::$status;
    }
    public static function critical($msg, $context = array(), $ch = 'log')
    {
        $fun = __FUNCTION__;
        !is_array($ch) ? $chs[] = $ch : $chs = $ch;
        foreach ($chs as $ch) {
            if (static::checkChannel($ch, $fun) !== false) {
                static::$log[$ch]->$fun($msg);
            }
        }
    }
    public static function __callStatic($method, $arguments)
    {
        if (!isset($arguments[1]) || !is_array($arguments[1])) {
            $arguments[1] = array();
        }
        $ch = !isset($arguments[2]) ? 'log' : $arguments[2];
        !is_array($ch) ? $chs[] = $ch : $chs = $ch;
        foreach ($chs as $ch) {
            if ($ch == 'log' && static::checkChannel($ch, $method) === false) {
                static::init();
            }
            if (static::checkChannel($ch, $method) !== false) {
                call_user_func_array(array(static::$log[$ch], $method), $arguments);
            }
        }
    }
    public static function customCommand($ch, $command, $arguments = array())
    {
        return call_user_func_array(array(static::$log[$ch], $command), $arguments);
    }
    protected static function checkChannel($ch, $type)
    {
        try {
            if (!isset(static::$log[$ch]) || !isset(static::$config[$ch]['type'])) {
                if ($ch == 'log') {
                    return false;
                }
                throw new \Exception('No channel '.$ch.' in MyLog!!!');
            }
            if (static::$status === false || !in_array($type, static::$config[$ch]['type'])) {
                return false;
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage()."\n".$e->getTraceAsString()."\n";
            if (!empty(static::$log)) {
                $ch = key(static::$log);
                static::critical($msg, array(), $ch);
            } else {
                die($msg);
            }
            return false;
        }
        return true;
    }
}
