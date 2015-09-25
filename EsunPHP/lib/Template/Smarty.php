<?php
/**
 * Smarty模版引擎
 * @author zhaomj
 */
final class Template_Smarty extends Template_Base{
    private $tpl;
    public function __construct(){
        $config=App_Info::config('TEMPLATE_CONFIG');
        if(isset($config['source_class'])){
            foreach($config['source_class'] as $path){
                include $path;
            }
        }
        if(!isset($config['smarty_version'])||$config['smarty_version']>2){
            $this->tpl=new Smarty();
        }else{
            $this->tpl=new SmartyBC();
        }
        if(isset($config['config'])){
            foreach($config['config'] as $k=>$v){
                $this->tpl->$k=$v;
            }
        }
        
        $this->_load_sys_func();
    }
    
    public static function format_url($params, $smarty){
        return call_user_func_array(array('App_Url', 'get'), $params);
    }
    
    private function _load_sys_func(){
        $this->tpl->registerPlugin('function', 'format_url', array($this, 'format_url'));
    }
    
    public function assign($v){
        $this->tpl->assign($v);
    }
    
    /**
     * 设置缓存
     * @param string $url_str controller/action
     * @param string $is_cache 缓存id
     * @param string $cache_time 编译id
     */
    public function set_cache($is_cache=true, $cache_time=null){
        $this->tpl->caching = $is_cache;
        if(!$is_cache) return false;
        
        if($cache_time!==null && $cache_time>-1){
            $this->tpl->cache_lifetime = $cache_time;
        }
    }
    
    public function get_handle(){
        return $this->tpl;
    }
    
    /**
     * 是否存在缓存
     * @param string $url_str controller/action
     * @param string $cache_id 缓存id
     * @param string $compile_id 编译id
     */
    public function is_cached($url_str=null, $cache_id=null, $compile_id=null){
        $temp = $this->template_file($url_str);
        return $this->tpl->isCached($temp, $cache_id, $compile_id);
    }
    
    
    public function fetch($tpl, $cache_id=null, $compile_id=null){
        return $this->tpl->fetch($tpl, $cache_id, $compile_id);
    }
    
}
