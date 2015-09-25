<?php
class Plugin_PageInfo implements Plugin_Interface{
    
    private $_start_info = array('time'=>0, 'memory'=>0);
    private $_stop_info = array('time'=>0, 'memory'=>0);
    
    public function func_service_start(){
        $this->_start_info['time'] = Common_Func::get_microtime();
        $this->_start_info['memory'] = memory_get_usage();
    }
    
    public function func_service_stop(){
        $this->_stop_info['time'] = Common_Func::get_microtime();
        $this->_stop_info['memory'] = memory_get_usage();
        
        self::show_result();
    }
    
    protected function show_result(){
        $cost_time = $this->_stop_info['time'] - $this->_start_info['time'];
        $cost_memory = $this->_stop_info['memory'] - $this->_start_info['memory'];
        
        $html = "<p style='padding:5px; text-align:center;margin:0px;";
        $html .= " color:#930; font-family: georgia;'>";
        $html .= "Cost Time:&nbsp;&nbsp;<span style='color:#06F'>%s</span>(s) | ";
        $html .= "Cost Memory:&nbsp;&nbsp;<span style='color:#06F'>%s</span>(bytes) | ";
        $html .= "<span style='color:#999;'>Powered By:EsunPHP:&nbsp;V%s</span>";
        $html .= "</p>";
        
        $str = sprintf($html, $cost_time, $cost_memory, ESUN_PHP_VERSION);
        $html = '<script>';
        $html .= '(function(){var d = document.createElement("div");';
        $html .= 'd.style.cssText = "position:fixed;bottom:0;display:block;width:100%;left:0;z-index:21;background:#EFEFEF;box-shadow:0 -1px 5px #BBBBBB;";';
        $html .= 'd.innerHTML = "'.$str.'";';
        $html .= 'document.body.appendChild(d);})();';
        $html .= '</script>';
        
        echo $html;
    }
    
    /**
     * 必须要实现的方法, 应用加载时执行
     * @see Plugin_Interface::setup()
     */
    public function setup(){
        App_Plugin::add_action(App_Key::$TAG_SERVICE_START, array($this, 'func_service_start'));
        App_Plugin::add_action(App_Key::$TAG_SERVICE_STOP, array($this, 'func_service_stop'));
    }
}