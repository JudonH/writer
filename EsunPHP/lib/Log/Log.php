<?php
/**
 * 
 * 日志基类
 * @author chenzf@500wan.com
 */
class Log_Log{    
	
    protected $_format = '[%s][%s]:%s [%s]';    //当前日志格式    
    protected $_record; //是否打印日志    
    protected $_level;  // 日志级别    
    protected $_levels; //所有日志级别    
    protected $_length; //每条日志最长长度 
    protected $_config; //日志配置
    protected $_content = '';   // 日志内容
    
    function __construct(){
    	$config = App_Info::config('LOG_CONFIG');
    	$this->_config = $config;
        $this->_record = $config['log_record'];
        $this->_level = $config['log_level'];
        $this->_length = $config['log_length'];
        $this->_levels = App_Key::$LOG_LEVELS;
    }
    
    /**
     * 保存日志
     * @param string    $message        日志内容
     * @param int       $level          日志级别
     * @throws App_Exception     
     * @return boolean  true/false
     */
    protected function _log($message, $level){
        if($this->_record!==true) return true;
        if($this->_levels[$this->_level]>$this->_levels[$level]) return true;        
        if($this->_length>0 && $this->length<strlen($message)){
            $message = substr($message, 0, $this->_length);
        }
        $message = sprintf($this->_format.PHP_EOL, date('Y-m-d H:i:s'), $level, $message, App_Info::$CURRENT_URL);
        $this->_content .= $message;
        return true;
    }

    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function debug($message){
        if(func_num_args()>1){
            $args = func_get_args();
            $args = is_array($args[1])?$args[1]:array_slice($args,1);
            $message = vsprintf($message,$args);
        }
        $this->_log($message,App_Key::$LOG_LEVEL_DEBUG);        
    }
    
    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function info($message){
        if(func_num_args()>1){
            $args = func_get_args();
            $args = is_array($args[1])?$args[1]:array_slice($args,1);
            $message = vsprintf($message,$args);
        }        
        $this->_log($message,App_Key::$LOG_LEVEL_INFO);     
    }

    /**
     * 写日志
     * @param string $message   日志内容
     */
    public function error($message){
        if(func_num_args()>1){
        	$args = func_get_args();
            $args = is_array($args[1])?$args[1]:array_slice($args,1);
            $message = vsprintf($message,$args);
        }
        $this->_log($message,App_Key::$LOG_LEVEL_ERROR);     
    }    
    
    /**
     * 写日志
     * @param $message  日志内容
     * @param $arr      替换数组
     * @param $type     日志级别
     */
    public function log_array($message,$arr,$level){
    	$this->$level($message,$arr);
    }
    
    /**
     * 保存日志内容，子类中重写
     */
    public function writer(){    	
    }
}     