<?php
/**
 *
 * 日志类
 * @author chenzf@500wan.com
 * @example
 *  $log = new Driver_Log();
 *  $log->info('log');
 *  $log->info('log %s','test');
 */
class Driver_Log{    
	
    private $_log = null;   //缓存类实例

    /**
     *
     * 构造函数
     * @param $log_type    日志类型（可以传入，也可以不传）
     */
    function  __construct($log_type=''){
        $this->_log = $this->_init($log_type);  
    }

    /**
     *
     * 初始化日志存储方式
     * @param $log_type 日志存储方式
     * @throws App_Exception
     * @return class
     */
    private function _init($log_type=''){
        if(empty($log_type)){
        	$config = App_Info::config('LOG_CONFIG');
            $log_type = $config['log_type'];
        }

        $log_type  = ucfirst(strtolower($log_type));
        $classname  = 'Log_'.$log_type;
        if(!class_exists($classname)){
            throw new App_Exception('类'.$classname.'不存在！', App_Key::$ERROR_KEY);
        }
        $class = Common_Func::get_instance($classname);
        return $class;
    }
    
    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function debug($message){
        $args = func_get_args();
        $this->_log->log_array($message,array_slice($args,1),'debug'); 
    }
    
    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function info($message){
    	$args = func_get_args();
        $this->_log->log_array($message,array_slice($args,1),'info');    
    }
    
    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function error($message){
        $args = func_get_args();
        $this->_log->log_array($message,array_slice($args,1),'error');   
    }  
}