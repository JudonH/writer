<?php
class App_Controller extends Core_Template{
    //控制器名
    protected $_controller;
    //操作名
    protected $_action;

    
    public function __construct(){
        $this->_controller = App_Info::$CONTROLLER_NAME;
        $this->_action = App_Info::$ACTION_NAME;
    }
    
    protected function error($msg, $code=-300){
        echo $msg; exit();
    }
    
    /**
     * 输出json字符串
     * @param object $data
     */
    protected function echo_json($data){
        if(!headers_sent()){
            $content_type = 'application/json';
            header('Content-Type:'.$content_type.'; charset='.App_Info::config('APP_CHARSET'));
        }
        echo json_encode($data);
	}
	
	/**
	 * header方法
	 * @param string $content
	 * @param string $replace
	 * @param string $http_response_code
	 */
	protected function header($content, $replace=true, $http_response_code=false){
	    $conx = explode(':', $content, 2);
	    if(strtoupper($conx[0]) == 'LOCATION' && isset($conx[1])){
	        $f = Common_Func::url_in_array_host(trim($conx[1]), App_Info::config('HOST_LOCATION_ALLOW'));
	        if(!is_null($f) && !$f){
	            $this->error('url跳转非法，可在URL_LOCATION_ALLOW配置');
	        }
	    }
	    
	    $header_func = function_exists('rename_function') ? Core_Utils::random_name('header') : 'header';
	    if(is_int($http_response_code)){
	        $header_func($content, $replace, $http_response_code);
	    }else{
	        $header_func($content, $replace);
	    }
	}
	
	/**
	 * 驱动器方法调用
	 * @see Core_Template::__get()
	 */
	public function __get($driver_name){
	    $cls = 'Driver_'.ucfirst($driver_name);
	    if(!class_exists($cls)){
	        throw new Core_Exception('Can\'t found driver:'.$driver_name, 
	        App_Key::$ERR_NOT_DRIVER_DEFINED);
	    }
	    //存起来
	    $ins = Common_Func::get_instance($cls);
	    $this->$driver_name = $ins;
	    return $ins;
	}
	
	/**
	 * 驱动器带初始化参数方法调用
	 * @param string $func driver name
	 * @param array $args driver constract params
	 * @throws Core_Exception
	 */
	public function __call($func, $args){
	    $cls = 'Driver_'.ucfirst($func);
	    if(!class_exists($cls)){
	        throw new Core_Exception('Can\'t found driver:'.$driver_name, 
	        App_Key::$ERR_NOT_DRIVER_DEFINED);
	    }
	    $ins = Common_Func::get_instance_array($cls, $args);
	    return $ins;
	}
	
}