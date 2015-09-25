<?php
class Template_Base{
    private $_template_file = array();
    
     /**
     * 获取模版文件
     * @param string $url_str controller/action
     */
    public function template_file($url_str=''){
        if(isset($this->_template_file[$url_str])){
            return $this->_template_file[$url_str];
        }
        
        $temp_ext = App_Info::config('TEMPLATE_EXT');
        if($url_str){
            $temp_file =str_replace('.', '/', $url_str);
            $temp_file = explode('/', $temp_file);
            if(trim($temp_file[0]) == ''){
                $temp_file[0] = App_Info::$CONTROLLER_NAME;
            }
            if(trim($temp_file[1]) == ''){
                $temp_file[1] = App_Info::$ACTION_NAME;
            }
        
            $temp_file = implode($temp_file, '/');
        }else{
            $temp_file = App_Info::$CONTROLLER_NAME.'/'.App_Info::$ACTION_NAME;
        }
        $temp_file = App_Info::config('TEMPLATE_PATH').$temp_file.'.'.$temp_ext;
        $this->_template_file[$url_str] = $temp_file;
        return $temp_file;
    }
    
    /**
     * 获取模版内容
     * @param string $url_str controller/action
     * @param string $cache_id 缓存id
     * @param string $compile_id 编译id
     */
    public function template_content($url_str='', $cache_id=null, $compile_id=null){
        $content='';
        $temp_file = $this->template_file($url_str);
        if (!$temp_file || !file_exists($temp_file)) {
            throw new Core_Exception('Template file['.$temp_file.'] can not found!', App_Key::$ERR_NOT_DEFINE_TEMPLATE);
            return ;
        }
        
        $content = $this->fetch($temp_file, $cache_id, $compile_id);
        
        return $content;
    }
    
    /**
     * 显示模板
     * @param string $temp_file
     * @param string $cache_id
     * @param string $compile_id
     */
    public function fetch($temp_file, $cache_id=null, $compile_id=null){
        
    }
    
    /**
     * 注册变量
     * @param array $parameters_array
     */
    public function assign($parameters_array){
        
    }
}