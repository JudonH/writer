<?php
/**
 * 
 * Memcached类
 * @author huangls
 *
 */
class Cache_Memcached{
    
    const CompressThreshold = 4096; // 压缩数据的阀值
    
    private $_memcache = null;
    private static $_instance = array();
    
    function __construct($host, $port=11211){
        $mc = new Memcache();
        $mc->addServer($host, intval($port));
        $mc->setCompressThreshold(self::CompressThreshold);
        $this->_memcache = $mc;
    }
    
    public function __call($func, $args){
        return call_user_func_array(array($this->_memcache, $func), $args);
    }
    
    /**
     * 是否可用
     */
    public function enabled(){
        return $this->_memcache ? true : false;
    }
    
    /**
     * 取得Memcache实例
     * 
     * @param string $cache_id 缓存配置id
     * @author huangls
     */ 
    public static function get_instance($cache_id='default'){
        if (!isset(self::$_instance[$cache_id])) {
            $conf = App_Info::config('CACHE_MEM_CONFIG');
            
            self::$_instance[$cache_id] = null;
            if(isset($conf[$cache_id])){
                $conf = $conf[$cache_id];
                $ins = new self($conf['host'], intval($conf['port']));
                self::$_instance[$cache_id] = $ins;
            }else{
                throw new App_Exception('memcached key:"'.$cache_id.'" not config in inc/config.php!', App_Key::$ERROR_KEY);
            }
        }
        return self::$_instance[$cache_id];
    }
    
    /**
     * 读取缓存
     *
     * @param string $id        缓存id
     * @param int    $flag     可以用MEMCACHE_COMPRESSED等，默认为0
     * @return array
     * @author huangls
     */
    public function get($id, $flag=0){
       $tmp = $this->_memcache->get($id, $flag);
       return $tmp;
    }
    
    /**
     * 保存缓存
     * @param string $id    数据对应的key
     * @param array  $data  要存的数据
     * @param int $lifetime 过期时间，0代表永不过期，默认0
     * @param int $flag     可以用MEMCACHE_COMPRESSED等，默认为0
     * @return bool 成功返回true，失败返回false
     * @author huangls
     */
    public function set($id, $data, $lifetime=0, $flag=0){
       $result = $this->_memcache->set($id, $data, $flag, $lifetime);
       return $result;
    }
    
    /**
     * 添加缓存，当缓存不存在时
     * @param string $id
     * @param mixed $data
     * @param int $lifetime
     */
    public function add($id, $data,$lifetime=0,$flag=0){
        $result = $this->_memcache->add($id, $data,$flag,$lifetime);
        return $result;
    }
    
    /**
     * 从memcache服务器删除数据
     *
     * @param mixed $id 数据对应的key
     * @param integer $timeout 延时多少时间删除数据
     * @return bool 成功返回true，失败返回false
     * @author huangls
     */
    public function del($id, $timeout=0){
        return $this->_memcache->delete($id, $timeout);
    }
    
    /**
     * 清除memcached缓存
     * @author huangls
     */
    public function clear($cache_type='user'){
        return $this->_memcache->flush();
    }
}
?>