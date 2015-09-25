<?php
/**
 * 
 * APC缓存类
 * @author huangls
 *
 */
class Cache_Apc{
    
    private static $_instance;
    
    public static function get_instance($cache_id=''){
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	/**
     * 是否可用
     */
    public function enabled(){
        return extension_loaded('apc');
    }
    
    /**
     * 读取缓存
     *
     * @param string $id
     * @return array
     * @author huangls
     */
    public function get($id){
        $tmp = apc_fetch($id);
        return $tmp;
    }
    
    /**
     * 保存缓存
     *
     * @param string $id
     * @param array  $data
     * @param int $lifetime
     * @return bool
     * @author huangls
     */
    public function set( $id, $data, $lifetime=0){
        $result = apc_store($id, $data, $lifetime);
        return $result;
    }
    
    /**
     * 添加缓存，当缓存不存在时
     *
     * @param string $id
     * @param array  $data
     * @param int $lifetime
     * @return bool
     * @author huangls
     */
    public function add( $id, $data, $lifetime=0){
        $result = apc_add($id, $data, $lifetime);
        return $result;
    }
    
    
    /**
     * 删除Id
     *
     * @param string $id  
     * @return bool
     * @author huangls
     */
    public function del($id){
        return apc_delete($id);
    }
    
    /**
     * 清除APC缓存 
     *
     * @param string $cache_type  user 如果 cache_type 是 "user", 用户 的缓存将被清除; 否则系统缓存(缓存文件)将被清除. 
     * @return bool
     * @author huangls
     */
    public function clear($cache_type='user'){
        return apc_clear_cache($cache_type);
    }
}
?>