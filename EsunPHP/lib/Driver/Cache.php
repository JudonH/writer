<?php
/**
 * 
 * 缓存类
 * @author huangls
 * @example
 * 数据缓存：
 *     $cache = new Driver_Cache('memcached', 'space');
 *     $cache->set($key,$data,$lifetime);
 *     $data = $cache->get($key);
 * 函数缓存：
 *     $fun = array($className,$funcionName);
 *     //$fun=$functionName;   如果只是想缓存函数而不是类函数，就这样使用
 *     $rs = Driver_Cache::call($fun,array($param1,$param2,..),$lifetime);
 */
class Driver_Cache
{
    //缓存类实例
    private $_cache = null;
    public $type = array();
    public $cache_id = 'default';
    
    private static $cache_ins = array();
    
    /**
     * 构造函数
     * @param $cachetype    缓存类型（可以传入，也可以不传）
     */
    function  __construct($cache_type='', $cache_id='default'){
        if(!$cache_type){
            $cache_type = App_Info::config('DEFAULT_CACHE_TYPE');
        }
        $this->type = $cache_type;
        $this->cache_id = $cache_id;
        $this->_cache = $this->_get_cache_ins();
    }
    
    /**
     * 重新连接一个
     * @param string $cache_id
     */
    public function connect($cache_id='default'){
        $id = $this->type.'|'.$cache_id;
        if(!isset(self::$cache_ins[$id])){
            self::$cache_ins[$id] = new self($this->type, $cache_id);
        }
        return self::$cache_ins[$id];
    }
    
    /**
     * 获取一个单例
     * @param string $type
     * @param string $cache_id
     */
    public static function get_instance($type='', $cache_id='default'){
        $id = $type.'|'.$cache_id;
        
        if(!isset(self::$cache_ins[$id])){
            self::$cache_ins[$id] = new self($type, $cache_id);
        }
        return self::$cache_ins[$id];
    }
    
    /**
     * 初始化Cache后端存储方式
     * @param  $cachetype   缓存类型
     * @throws Exception    
     * @return class
     * @author huangls
     */
    private function _get_cache_ins(){
        $type  = ucfirst(strtolower($this->type));
        $classname  = 'Cache_'.$type;
        if(!class_exists($classname)){
            throw new App_Exception($classname.' not exist!', App_Key::$ERROR_KEY);
        }
        $class = call_user_func_array(array($classname, 'get_instance'), array('cache_id'=>$this->cache_id));
        return $class;
    }
    
    /**
     * 函数缓存方法
     * @param string/array  $fun        缓存函数名
     * @param array         $param      缓存函数参数
     * @param int           $lifetime   缓存时间
     * @return $data
     * @author huangls
     */
    public function call($fun, $param, $lifetime){
        $id   = self::make_id($fun, $param);
        $data = $this->_cache->get($id);
        //如果存在第一份缓存，直接返回
        if($data){
            return $data;
        }
        
        $data = call_user_func_array($fun, $param);
        $this->_cache->add($id, $data, $lifetime);
        return $data;
    }
    
    /**
     * 获取函数id
     * @param array/string $fun
     * @param array $param
     * @throws Exception
     * @return string
     * @author huangls
     */
    private static function make_id($func, $param){
        if (!is_callable($func, true, $name)) {
            throw new App_Exception($func.' not callable!', App_Key::$ERROR_KEY);
        }
        
        $param = $param ? sha1(implode('',array_values($param))) : '';
        
        //区分不同站点的同名函数
        $name = '__'.Common_Func::env('HTTP_HOST').'__'.$name.'__'.$param;
        return $name;
    }
    
    /**
     * 读取缓存
     * @param string $id    缓存key
     * @return data        缓存数据
     * @author huangls
     */
    public function get($id)
    {
        return $this->_cache->get($id);
    }
    
    /**
     * 设置缓存
     *
     * @param string $id    缓存key
     * @param array  $data  缓存数据
     * @param int $lifetime 缓存时间
     * @return bool
     * @author huangls
     */
    public function set($id,$data, $lifetime)
    {
        return $this->_cache->set($id,$data,$lifetime);
    }
    
    /**
     * 设置缓存，当id不存在时成功
     * 
     * @param string $id    缓存key
     * @param array  $data  缓存数据
     * @param int $lifetime 缓存时间
     * @return bool
     * @author huangls
     */
    public function add($id,$data, $lifetime)
    {
        return $this->_cache->add($id,$data,$lifetime);
    }
    
    /**
     * 删除Id
     * @param string $id    缓存key
     * @return bool         是否删除成功标志
     * @author huangls
     */
    public function del($id)
    {
        return $this->_cache->del($id);
    }
    
    /**
     * 清除缓存 
     * @param string $cache_type    类型
     * @return bool                 是否清除成功标志
     * @author huangls
     */
    public function clear($cache_type="user")
    {
        return $this->_cache->clear($cache_type);
    }
    
    /**
     * 调用别的方法 
     * @param string $func    方法
     * @param array $args    参数
     * @author huangls
     */
    public function __call($func, $args){
        return call_user_func_array(array($this->_cache, $func), $args);
    }
}