<?php
class Driver_Session{
    
	private static $session_on = false;
    /**
     * 初始化函数中打开session
     */
	public static function init(){
		/* 会多发送setcookie @2013-11-1
		if($sid=Driver_Cookie::get(session_name())){
		    session_id($sid);
		}
		*/
		if(!self::$session_on && !session_id()) {
			@session_start(); 
			self::$session_on=true;
		}
	}
	
	/**
	 * 设置session
	 * @param string $key
	 * @param string $value
	 */
	public static function set($key, $value){
		self::init();
		
		$_SESSION[$key] = $value;
		return isset($_SESSION[$key]);
	}
	
	/**
	 * 获取session值
	 * @param string $key
	 * @param mixed $default_val 缺省值
	 */
	public static function get($key, $default_val=null){
		self::init();
		
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default_val;
	}
	
	/**
	 * 检查是否存在
	 * @param string $key
	 */
	public static function exist($key){
		self::init();
		
	    return isset($_SESSION[$key]);
	}
	
	/**
	 * 设置session过期时间
	 * @param mixed $cache_expire null将返回过期时间，int将设置过期时间
	 */
	public static function expire($cache_expire = null){
		self::init();
		
	    //返回过期时间
	    if(is_null($cache_expire)) return session_cache_expire();
	    
	    //设置过期时间
	    $cache_expire = intval($cache_expire);
	    session_cache_expire($cache_expire);
	    return $cache_expire;
	}
	
	/**
	 * 删除session值
	 * @param string $key
	 */
	public static function delete($key){
		self::init();
		
		unset($_SESSION[$key]);
	}
	
	/**
	 * 彻底删除session
	 */
	public static function clear(){
		self::init();
		
	    $_SESSION = array();
	    $sname = session_name();
	    if (Driver_Cookie::exist($sname)){
	        Driver_Cookie::set($sname, '', time()-42000, '/');
        }
        // 最后彻底销毁session.
        session_destroy();
	}
}