<?php
class Driver_Input{
    
    private static $_args_conf = array();
    
    private $_type = 'request';
    private $_input = array();
    
	/**
	 * 构造函数
	 * @param string $type 类型
	 */
    function  __construct($type='get'){
        $this->_type = $type;
        $this->_input = self::$_args_conf[$type];
    }
    
    /**
     * 系统启动时会销毁该值
     */
    public static function init(){
        if(isset($_GET)){
            self::$_args_conf['get'] = $_GET;
            unset($_GET);
        }
        if(isset($_POST)){
            self::$_args_conf['post'] = $_POST;
            unset($_POST);
        }
        if(isset($_REQUEST)){
            self::$_args_conf['request'] = $_REQUEST;
            unset($_REQUEST);
        }
    }
    
    /**
     * 是否存在某key
     * @param string $key
     */
    public function isset_key($key){
        return isset($this->_input[$key]);
    }
    
    /**
     * 去除html标签
     * @param string $key
     * @param mixed $dval
     */
    public function strip_tags($key, $dval=null){
        $val = $this->get_string($key, $dval);
        return $val ? strip_tags($val) : $val;
    }
    
    /**
     * 转义html标签
     * @param string $key
     * @param mixed $dval
     */
    public function htmlspecialchars($key, $dval=null){
        $val = $this->get_string($key, $dval);
        return $val ? htmlspecialchars($val) : $val;
    }
    
	/**
	 * 检查整型
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_int($key, $dval=null){
       if(!isset($this->_input[$key])) return $dval;
       $value = $this->_input[$key];
        if(is_int($value)){
            return $value;
        }
        else{ 
            $rs = intval($value);
             return $value!=='0' && $rs===0 ? $dval:$rs;
        }
    }
    
    
	/**
	 * 检查浮点型
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_float($key, $dval=null){
       if(!isset($this->_input[$key])) return $dval;
       $value   =   $this->_input[$key];
        if(is_float($value)){
            return $value;
            
        }else{ 
            $rs = floatval($value);
            return $value!==0.0 && $rs===0.0 ?  $dval : $rs;
        }
    }
    
	/**
	 * 检查字符型
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_string($key, $dval=null){
       if(!isset($this->_input[$key])) return $dval;
       $value = $this->_input[$key];
        if(is_string($value) && $value!==''){
            return $value;
            
        }else{ 
             settype($value,'string');
             $rs = $value;
             return $rs==='' ? $dval : $rs;
        }
    }
	/**
	 * 检查字符型
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_array($key, $dval=null){
       if(!isset($this->_input[$key])) return $dval;
       $value = $this->_input[$key];
        if(is_array($value) && $value!==array()){
            return $value;
            
        }else{
             settype($value, 'array');
             $rs = $value;
             return $rs===array() ? $dval : $rs;
        }
    }
	/**
	 * 检查邮箱
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_email($key, $dval=null){
       if(!isset($this->_input[$key])) return $dval;
       $value = $this->_input[$key];
        $patter = "/^[\w\-\.]+@[\w\-]+(\.\w+)+$/D";
        return strlen($value) > 6 && preg_match($patter, $value) ? $value : $dval;
    }
	/**
	 * 检查URL
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_url($key, $dval=null){
        if(!isset($this->_input[$key])) return $dval;
        $value = $this->_input[$key];
        $patter = "/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/D";
        return preg_match($patter, $value) ? $value : $dval;
    }
    
	/**
	 * 检查电话
	 *
	 * @param string $value	检查内容
	 * @param mixed $dval	默认内容
	 * @return string
	 */
    public function get_mobile($key, $dval=null){
        if(!isset($this->_input[$key])) return $dval;
        $value = $this->_input[$key];
        $patter = "/^1\d{10}$/D";
        return preg_match($patter, $value) ? $value : $dval;
    }
    
    /**
     * 初始化本类，用于 Driver_Input::get_instance('get')->get_int();
     *
     * @param string $type
     * @return object
     */
    public static function get_instance($type='get'){ 
        static $ins_conf = array();
        if(!isset($ins_conf[$type])){
            $ins_conf[$type] = new self($type);
        }
        
        return $ins_conf[$type];
    }
}