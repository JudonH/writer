<?php
/**
 * 
 * Redis缓存类
 * @author huangls
 *
 */
class Cache_Redis
{
    private $_redis = null;
    private static $_instance = array();
    
    public function __call($func, $args){
        return call_user_func_array(array($this->_redis, $func), $args);
    }
    
    public function __construct($host, $port=6379){
        $this->_redis = new Redis();
        $this->_redis->connect($host, $port);
    }
    
	/**
     * 是否可用
     */
    public function enabled(){
        return $this->_redis ? true : false;
    }
    
    /**
     * 返回redis对象 
     * redis有非常多的操作方法，我们只封装了一部分 
     * 拿着这个对象就可以直接调用redis自身方法
     * 
     * @param string $cache_id 缓存配置id
     */ 
    public static function get_instance($cache_id='default'){
        if (!isset(self::$_instance[$cache_id])) {
            $conf = App_Info::config('CACHE_REDIS_CONFIG');
            self::$_instance[$cache_id] = null;
            if(isset($conf[$cache_id])){
                $conf = $conf[$cache_id];
                $ins = new self($conf['host'], intval($conf['port']));
                self::$_instance[$cache_id] = $ins;
            }else{
                throw new App_Exception('redis key:"'.$cache_id.'" not config in inc/config.php!', App_Key::$ERROR_KEY);
            }
        }
        return self::$_instance[$cache_id];
    }
    
    /**
     * 读取缓存
     *
     * @param string $id
     * @return array
     * @author huangls
     */
    public function get($id){
        $result = $this->_redis->get($id);
        return $result;
    }
    
    /**
     * 保存缓存
     *
     * @param string $id
     * @param string $data
     * @param int $lifetime
     * @return bool
     * @author huangls
     */
    public function set($id, $data, $lifetime=0){
        $ret = $this->_redis->set($id, $data);
        if ($lifetime > 0) $this->_redis->setTimeout($id, $lifetime);
        return $ret;
    }
    
    /**
     * 添加缓存，当缓存不存在时
     * @param string $id
     * @param mixed $data
     * @param int $lifetime
     */
    public function add($id, $data, $lifetime=0){
        $ret = $this->_redis->setnx($id, $data);
        if ($lifetime > 0) $this->_redis->setTimeout($id, $lifetime);
        return $ret;
    }
    
    /**
     * 删除Id
     *
     * @param string $id  
     * @return bool
     * @author huangls
     */
    public function del($id){
         return $this->_redis->delete($id);    
    }
    
    /**
     * 清空数据 
     *
     * @param string $cache_type 
     * @return bool
     * @author huangls
     */
    public function clear($cache_type='user'){
        return $this->_redis->flushAll();
    }
}