<?php

/**
 * URL调度分析类
 * @author tuyl
 */
class Core_Dispatcher{
    
    public static function init(){
        App_Info::$CONTROLLER_NAME = App_Info::config('DEFAULT_CONTROLLER_NAME');
        App_Info::$ACTION_NAME = App_Info::config('DEFAULT_ACTION_NAME');
        
        $is_cgi = substr(PHP_SAPI, 0,3)=='cgi';
        if($is_cgi){//CGI/FASTCGI模式下
            $temp  = explode('.php', $_SERVER['PHP_SELF']);
            App_Info::$CURRENT_URL = $_SERVER['PHP_SELF'];
            App_Info::$INDEX_URL = rtrim(str_replace($_SERVER['HTTP_HOST'], '', $temp[0].'.php'), '/');
        }else {
            $info = pathinfo($_SERVER['SCRIPT_NAME']);
            $url = strpos($_SERVER['REQUEST_URI'], $info['dirname'])===0 ? $_SERVER['SCRIPT_NAME'] : '/'.$info['basename'];
            App_Info::$CURRENT_URL = $_SERVER['REQUEST_URI'];
            App_Info::$INDEX_URL = $url;
        }
        
        //兼容重写出现 /index.php/controller/action
        isset($_SERVER['HTTP_X_REWRITE_URL']) && App_Info::$CURRENT_URL=$_SERVER['HTTP_X_REWRITE_URL'];
        
        App_Info::$BASE_URL = rtrim(dirname(App_Info::$INDEX_URL), DIRECTORY_SEPARATOR);
        
        //把本域加入配置
        $allow = App_Info::config('HOST_LOCATION_ALLOW');
        $allow[] = $_SERVER['HTTP_HOST'];
        App_Info::config('HOST_LOCATION_ALLOW', $allow);
        $allow = App_Info::config('HOST_LOCATION_ALLOW');
    }
    
    public static function dispatch(){
        // 分析PATHINFO信息
        if(!isset($_SERVER['PATH_INFO'])) {
            $_SERVER['PATH_INFO'] = '';
            $types   =  explode(',', 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL');
            foreach ($types as $type){
                if(!empty($_SERVER[$type])) {
                    $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type], $_SERVER['SCRIPT_NAME']))?
                        substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME'])) : $_SERVER[$type];
                    break;
                }
            }
        }
        
        if($url_str = $_SERVER['PATH_INFO']) {
            //取出伪静态扩展名 index.php/xx.html
            $part =  pathinfo($_SERVER['PATH_INFO']);
            
            $url_ext = isset($part['extension']) ? strtolower($part['extension']) : '';
            if($url_ext && $allow_ext=App_Info::config('URL_SUFFIX_ALLOW')) { //提取扩展名
                $allow = array_search($url_ext, $allow_ext);
                if($allow !== false){
                    App_Info::$URL_EXT = $url_ext;
                    $_SERVER['PATH_INFO'] = preg_replace('/.'.App_Info::$URL_EXT.'$/i', '', $_SERVER['PATH_INFO']);
                    $url_str = preg_replace('/.'.App_Info::$URL_EXT.'$/i', '', $url_str);
                }
            }
            $regx = trim($url_str, '/');
            
            $var = array();
            // 检测路由规则 如果没有则按默认规则调度URL
            if(!App_Info::config('URL_ROUTE_ON')){
                
                self::decode_url_str($regx);
            }else{
                self::check_route($regx);
            }
        
        }else{ //PATH_INFO 空
            $var = $_REQUEST;
            $con_val = App_Info::config('PATH_CONTROLLER_VAR');
            if(isset($var[$con_val])) {
                App_Info::$CONTROLLER_NAME =  strip_tags($var[$con_val]);
                unset($var[$con_val]);
            }
            
            $act_val = App_Info::config('PATH_ACTION_VAR');
            if(isset($var[$act_val])) {
                App_Info::$ACTION_NAME = strip_tags($var[$act_val]);
                unset($var[$act_val]);
            }
        }
        App_Info::$CURRENT_URL_STR = App_Info::$CONTROLLER_NAME.'/'.App_Info::$ACTION_NAME.'?'.http_build_query($var);
    }
    
    /**
     * 路由检查
     */
    public static function check_route($url_path){
        if(!App_Info::config('URL_ROUTE_ON')) return ;
        App_Info::$CONTROLLER_NAME = '';
        App_Info::$ACTION_NAME = '';
        foreach (App_Info::config('URL_ROUTE_CONFIG') as $rule => $route) {
            //强制要求正则以/开头，以便扩展
            if(strpos($rule, '/')===0 && preg_match($rule, $url_path, $matches)) { // 正则路由
                return self::parse_url($url_path, $route, $matches);
            }
        }
        
        self::decode_url_str($url_path);
    }
    
    private static function decode_url_str($paths){
        $paths = explode(App_Info::config('URL_PATHINFO_SEPARATOR'), $paths);
        App_Info::$CONTROLLER_NAME = isset($paths[0])&&$paths[0] ? strip_tags(array_shift($paths)) : App_Info::config('DEFAULT_CONTROLLER_NAME'); 
        App_Info::$ACTION_NAME = isset($paths[0])&&$paths[0] ? strip_tags(array_shift($paths)) : App_Info::config('DEFAULT_ACTION_NAME'); 
        $var = array();
        // 解析剩余的URL参数
        preg_replace('@(\w+)\/([^\/]+)@e', '$var[\'\\1\']=strip_tags(\'\\2\');', implode('/', $paths));
        $_GET   =  array_merge($var, $_GET);
        $_REQUEST = array_merge($var, $_REQUEST);
    }
    
    /**
     * 路由正则检查
     * @param string $regx
     * @param string $route
     * @param array $matches
     */
    public static function parse_url($regx, $route, $matches){
        $url = is_array($route) ? $route[0] : $route;
        $url   =  preg_replace('/\$(\d+)/e','$matches[\\1]', $url);
        //绝对路径或者http url则跳转
        if(stripos($url, '/') === 0 || substr($url, 0, 4) == 'http://'){
            $http_code = (is_array($route) && isset($route[1])) ? $route[1] : 301;
            header('Location: '.$url, true, $http_code);
            exit();
        }else{
            $info = App_Url::info($url);
            App_Info::$CONTROLLER_NAME = $info['c_name'];
            App_Info::$ACTION_NAME = $info['a_name'];
            $_GET   =  array_merge($info['params'], $_GET);
            $_REQUEST = array_merge($info['params'], $_REQUEST);
        }
    }
}
