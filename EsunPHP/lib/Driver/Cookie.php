<?php
class Driver_Cookie{
    
    private static $_cookie = null;
    
    /**
     * 构造方法
     */
    function __construct(){
        self::init();
    }
    
    /**
     * 系统启动时会销毁该值
     */
    public static function init(){
        if(isset($_COOKIE)){ //多次构造该值会失效
            self::$_cookie = $_COOKIE;
            //unset($_COOKIE);
        }
    }
    
    /**
     * 获取cookie值
     * @param string $key COOKIE键
     */
    public static function get($key){
        return isset(self::$_cookie[$key]) ? self::$_cookie[$key] : false;
    }
    
    /**
     * 设置cookie值
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     */
    public static function set($key, $value, $expire = null, $path = null, $domain = null, $secure = false){
        return setcookie($key, $value, $expire, $path, $domain, $secure);
    }
    
    /**
     * 检查是否存在
     * @param string $key
     */
    public static function exist($key){
        return isset(self::$_cookie[$key]);
    }
    
    /**
     * 删除cookie键
     * @param string $key
     */
    public static function delete($key){
        self::set($key, '', time()-3600);
    }
    
    /**
     * 清除全部cookie
     */
    public static function clear(){
        foreach (self::$_cookie as $k=>$v) {
            self::delete($k);
        }
    }
}