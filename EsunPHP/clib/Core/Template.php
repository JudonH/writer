<?php
class Core_Template{
    
    private $_params = array();
    protected $_template = null;
    private $_header = array('charset'=>null, 'content_type'=>'text/html');
    
    /**
     * 设置发送头信息
     * @param string $url_str controller/action
     * @param string $cache_id 缓存id
     * @param string $compile_id 编译id
     */
    public function set_header($charset=null, $content_type=null){
        $charset && $_header['charset']=$charset;
        $content_type && $_header['content_type']=$content_type;
    }
    
    /**
     * 获取template对象
     */
    public function template_view(){
        if($this->_template != null){
            return $this->_template;
        }
        
        $temp_name = 'Template_'.App_Info::config('TEMPLATE_TYPE');
        $this->_template = Common_Func::get_instance($temp_name);
        if(!$this->_template){
            throw new Core_Exception('Template['.$temp_name.'] Type Error!', App_Key::$ERR_NOT_DEFINE_TEMPLATE);
        }
        return $this->_template;
    }
    
     /**
     * 解析显示模板
     * @param string $url_str controller/action
     * @param string $cache_id 缓存id
     * @param string $compile_id 编译id
     */
    public function render($url_str=null, $cache_id=null, $compile_id=null){
        $charset = $this->_header['charset'] ? $this->_header['charset'] : App_Info::config('APP_CHARSET');
        $content_type = $this->_header['content_type'];
        
        if(!headers_sent()){ //如果之前没有输出
            header('Content-Type:'.$content_type.'; charset='.$charset);
            header('X-Powered-By:'.ESUN_PHP_NAME);
        }
        
        $temp = $this->template_view();
        $temp->assign($this->_params);
        
        echo $temp->template_content($url_str, $cache_id, $compile_id);
    }
    
    /**
     * 注册变量
     * @param string || array $name
     * @param object $value 
     */
    public function assign($name, $value=''){
        if(is_array($name)) {
            $this->_params = array_merge($this->$_params, $name);
        
        }elseif($name = trim($name)){
            $this->_params[$name] = $value;
        }
    }
    
    public function __set($name, $value){
        $this->assign($name, $value);
    }
    
    public function __get($name){
        return isset($this->_params[$name]) ? $this->_params[$name] : false;
    }
    
}
