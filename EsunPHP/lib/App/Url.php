<?php
/**
 * URL格式化类
 * @example $url = App_Url::get('controller/action?key=val', 'k2=v2&k3=v3', '.html');
 *          $url = App_Url::common('controller/action?key=val', 'k2=v2&k3=v3');
            $url = App_Url::path('controller/action?key=val', 'k2=v2&k3=v3', '.html');
 * @author tuyl
 */
class App_Url{
    
    private static function _route_get($c, $a, $vars=array(), $url_suffix=false){
        
        foreach (App_Info::config('URL_ROUTE_CONFIG') as $rule => $route) {
            $url = is_array($route) ? $route[0] : $route;
            $info = self::info($url);
            if($info['c_name'] != $c || $info['a_name'] != $a)
                continue;
            
            $r_keys = array_keys($info['params']);
            $p_keys = array_keys($vars);
            if($r_keys != $p_keys) continue;
            
            $ret = array();
            foreach($p_keys as $v){
                $ret[$info['params'][$v]] = $vars[$v]; 
            }
            
            if(!isset($route[1])) continue;
            
            ksort($ret);
            $val_mat = array_values($ret);
            $url = preg_replace('/\$(\d+)/e','$val_mat[\\1-1]', $route[1]);
            return $url;
        }
        
        return false;
    }
    
    private static function _get($url='', $vars='', $url_suffix=false, $url_type = ''){
        $info = self::info($url);
        
        // 解析参数
        if(is_string($vars)){ // aaa=1&bbb=2 转换成数组
            parse_str($vars, $vars);
        }elseif(!is_array($vars)){
            $vars = array();
        }
        
        $vars = array_merge($info['params'], $vars);
        $var = array(App_Info::config('PATH_CONTROLLER_VAR')=>$info['c_name'], 
            App_Info::config('PATH_ACTION_VAR')=>$info['a_name']);
        
        $url = App_Info::$INDEX_URL; $r_url = null;
        if(App_Info::config('URL_ROUTE_ON')){
            $url = App_Info::$BASE_URL;
            $r_url = self::_route_get($info['c_name'], $info['a_name'], $vars);
        }
        
        if(!$r_url){
            switch ($url_type){
                case App_Key::$URL_TYPE_PATH:
                    $url = $url.'/'.implode(App_Info::config('URL_PATHINFO_SEPARATOR'), $var);
                    if($vars) {
                        foreach ($vars as $k => $v)
                            $url .= App_Info::config('URL_PATHINFO_SEPARATOR').$k.App_Info::config('URL_PATHINFO_SEPARATOR').$v;
                    }
                    break;
                
                case App_Key::$URL_TYPE_COMMON:
                    $url = $url.'?'.http_build_query($var);
                    if(!empty($vars)) {
                        $vars = urldecode(http_build_query($vars));
                        $url .= '&'.$vars;
                    }
                    break;
            }
        }else{
            $url .= $r_url;
        }
        
        if($url_suffix !== false){
            $url .= $url_suffix;
        }else{
            $url .= App_Info::config('URL_SUFFIX');
        }
        return $url;
    }
    
    /**
     * 获取url详细
     * @param string $url_str
     */
    public static function info($url_str=''){
        $info = parse_url($url_str);
        $url = !empty($info['path']) ? $info['path'] : 
            App_Info::$CONTROLLER_NAME.'/'.App_Info::$ACTION_NAME;
        
        $url_info = explode('/', $url);
        $c = isset($url_info[0]) && $url_info[0] ? 
            strip_tags($url_info[0]) : App_Info::$CONTROLLER_NAME;
        $a = isset($url_info[1]) && $url_info[1] ? 
            strip_tags($url_info[1]) : App_Info::$ACTION_NAME;
        
        // 解析地址里面参数 合并到vars
        $params = array();
        if(isset($info['query'])) { 
            parse_str($info['query'], $params);
        }
        
        return array('c_name'=>$c, 'a_name'=>$a, 'params'=>$params);
    }
    
    /**
     * 获取默认配置类型的url
     * @param string $url controller/action[?k1=v1]
     * @param string $vars k2=v2&k3=v3
     * @param string $url_suffix .html
     */
    public static function get($url='', $vars='', $url_suffix=false){
        return self::_get($url, $vars, $url_suffix, App_Info::config('URL_TYPE'));
    }
    
    /**
     * 获取带hashkey的url
     * @param string $url controller/action[?k1=v1]
     * @param string $vars k2=v2&k3=v3
     * @param string $url_suffix .html
     */
    public static function hash_url($url='', $vars='', $url_suffix=false, $hash_key='__hash'){
        $hstr = $hash_key.'='.self::get_hash_value();
        $vars = $vars ? $vars.'&'.$hstr : $hstr;
        return self::_get($url, $vars, $url_suffix, App_Info::config('URL_TYPE'));
    }
    
    /**
     * 检查hashkey是否合法性
     * @param string $hash_key
     * @param boolean $to404
     */
    public static function check_hash_url($hash_key='__hash', $to404=true){
        $input = Driver_Input::get_instance('request');
        $hash = $input->get_string($hash_key);
        $flag = $hash === self::get_hash_value();
        if(!$flag && $to404){
            Common_Func::send_http_status(404);
            return false;
        }
        
        return $flag;
    }
    
    /**
     * 生成一个和session_id相关的hash值
     */
    public static function get_hash_value(){
        $val = App_Info::$BASE_URL.'|'.session_id();
        return md5($val);
    }
    
    /**
     * 获取普通类型的url
     * @param string $url controller/action[?k1=v1]
     * @param string $vars k2=v2&k3=v3
     */
    public static function common($url='', $vars=''){
        return self::_get($url, $vars, '', App_Key::$URL_TYPE_COMMON);
    }
    
    /**
     * 获取路径类型的url
     * @param string $url controller/action[?k1=v1]
     * @param string $vars k2=v2&k3=v3
     * @param string $url_suffix .html
     */
    public static function path($url='', $vars='', $url_suffix=false){
        return self::_get($url, $vars, $url_suffix, App_Key::$URL_TYPE_PATH);
    }
}
