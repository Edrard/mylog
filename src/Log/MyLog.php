<?php

namespace edrard\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MyLog
{
    protected static $log = array();
    protected static $status = TRUE; // if True, then ON, if False, then OFF 
    /**
    * Numeric presset type of logs 0 - info, 1 - warning, 2 - error, 3 - critical
    * 
    * @var mixed
    */
    protected static $array_type_base = array('info','warning','error','critical');
    protected static $array_type = array();

    public static function init($path = 'logs',$ch = 'log', array $handlers = array(),$re_enable = FALSE){
        if(!isset(static::$log[$ch]) || $re_enable !== FALSE){
            static::$log[$ch] = new Logger($ch);
            static::$array_type[$ch] = static::$array_type_base;
            if($re_enable === FALSE){ 
                static::$log[$ch]->pushHandler(new StreamHandler($path.'/debug.log', Logger::DEBUG, false));
                static::$log[$ch]->pushHandler(new StreamHandler($path.'/info.log', Logger::INFO, false));
                static::$log[$ch]->pushHandler(new StreamHandler($path.'/error.log', Logger::WARNING, false));
                static::$log[$ch]->pushHandler(new StreamHandler($path.'/error.log', Logger::ERROR, false));
                static::$log[$ch]->pushHandler(new StreamHandler($path.'/error.log', Logger::CRITICAL, false));  
            }
            foreach($handlers as $handel){
                if($handel instanceof StreamHandler){
                    static::$log[$ch]->pushHandler($handel);    
                }
            }
        }   
    }
    public static function changeType($array_type = array('info','warning','error','critical'),$ch = 'log'){
        static::$array_type[$ch] = $array_type;
    }
    public static function changeStatus($status = TRUE){
        static::$status = $status;
    }
    public static function status(){
        return static::$status;
    }
    public static function critical($msg,$context = array(),$ch = 'log'){
        $fun = __FUNCTION__;
        if(static::checkChannel($ch,$fun) !== FALSE){
            static::$log[$ch]->$fun($msg);          
        } 

    }
    public static function __callStatic($method,$arguments) {
        if(!isset($arguments[1]) || !is_array($arguments[1])){
            $arguments[1] = array();
        }        
        $ch = !isset($arguments[2]) ? 'log' : $arguments[2]; 
        if($ch == 'log' && static::checkChannel($ch,$method) === FALSE){
            static::init();    
        }
        if(static::checkChannel($ch,$method) !== FALSE){       
            call_user_func_array(array(static::$log[$ch], $method), $arguments); 
        } 
    }
    public static function allTypes($method){
        return static::array_type[$method];
    }
    protected static function checkChannel($ch,$type){ 
        try{
            if(!isset(static::$log[$ch]) || !isset(static::$array_type[$ch])){
                throw new \Exception('No channel '.$ch.' in MyLog!!!');
            }
            if(static::$status === FALSE || !in_array($type,static::$array_type[$ch])){
                return FALSE;
            }
        }catch(\Exception $e){
            $msg = $e->getMessage()."\n".$e->getTraceAsString()."\n";
            if(!empty(static::$log)){
                $ch = key(static::$log);
                static::critical($msg,array(),$ch);

            }else{
                die($msg);
            }               
            return FALSE;
        } 
        return TRUE;    
    }
}