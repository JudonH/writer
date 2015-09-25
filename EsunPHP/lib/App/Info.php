<?php
class App_Info{
    //EsunPHP版本
    public static $VERSION = ESUN_PHP_VERSION;
    
    //URL扩展符
    public static $URL_EXT = 'html';
    
    //控制器名称
    public static $CONTROLLER_NAME = '';
    //操作名称
    public static $ACTION_NAME = '';
    
    //执行时间
    public static $EXCT_TIME = 0;
    
    //主页url，带index.php
    public static $INDEX_URL = '';
    
    //基本URL
    public static $BASE_URL = '';
    
    //当前URL
    public static $CURRENT_URL= '';
    
    //当前URL字符形式, 如:controller/method?key=value
    public static $CURRENT_URL_STR = '';
    
    /**
     * 应用配置
     * @param string||array $key
     * @param object $val
     */
    public static function config($key=null, $val=null){
        static $_config = array();
        if(is_array($key)){ //批量设置
            $_config = array_merge($_config, $key);
            return $val;
        }elseif($val===null){ //读取配置
            return $key===null ? $_config : $_config[$key];
        }elseif(is_string($key)){ //设置配置
            $_config[$key] = $val;
            return $val;
        }
        return $val;
    }
}



