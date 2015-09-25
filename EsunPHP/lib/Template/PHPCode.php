<?php
final class Template_PHPCode extends Template_Base{
    
    private $_params = array();
    
    /**
     * 输出生成的html结果
     * @see Template_Interface::display()
     */
    public function fetch($temp_file, $cache_id=null, $compile_id=null){
        // 页面输出缓存
        ob_start();
        ob_implicit_flush(0);
        
        if ($this->_params) {
            //此处不会存在变量覆盖漏洞，作用域限制
            extract($this->_params, EXTR_PREFIX_SAME, 'i_');
        }
        include($temp_file);
        
        // 获取缓存
        $content = ob_get_clean();
        return $content;
    }
    
    
    /**
     * 注册变量到模板中
     * @see Template_Interface::assign()
     */
    public function assign($para_array){
        $this->_params = $para_array;
    }
}