<?php

namespace edrard\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MyLog
{
    protected static $log = array();
    /**
    * Numeric presset type of logs 0 - info, 1 - warning, 2 - error, 3 - critical
    * 
    * @var mixed
    */
    protected static $array_type = array('info','warning','error','critical');

    public static function init($path = 'logs',$ch = 'log', array $handlers = array(),$re_enable = FALSE){
        if(!isset(static::$log[$ch]) || $re_enable !== FALSE){
            static::$log[$ch] = new Logger($ch);
            if($re_enable === FALSE){ 
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
    public static function critical($msg,$context = array(),$ch = 'log'){
        $fun = __FUNCTION__;
        if(static::checkChannel($ch) !== FALSE){
            static::$log[$ch]->$fun($msg);          
        } 

    }
    public static function __callStatic($method,$arguments) {
        if(!isset($arguments[1]) || !is_array($arguments[1])){
            $arguments[1] = array();
        }        
        $ch = !isset($arguments[2]) ? 'log' : $arguments[2]; 
        if(static::checkChannel($ch) !== FALSE){       
            call_user_func_array(array(static::$log[$ch], $method), $arguments); 
        } 
    }
    public static function allTypes($method){
        return static::array_type[$method];
    }
    protected static function checkChannel($ch){
        try{
            if(!isset(static::$log[$ch])){
                throw new \Exception('No channel '.$ch.' in MyLog!!!');
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