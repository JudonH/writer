<?php
/**
 * 访问控制类
 * @author nathen
 * @date 2012-11-29
 */

Class Limit_Visit {
    static private $_instance = null;
    
    //存储次数的键名
    private $_key = 'limit_default_key';
    
    //间隔时间
    private $_interval = 1;
    
    //访问次数
    private $_count = 200;
    
    /**
     * 获取单例
     */
    public static function get_instance(){
        if(!self::$_instance){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 初始化限制类
     * @param string $key 限制key，建议用接口名
     * @param int $interval 限制频率
     * @param int $count 限制次数
     */
    private function _init($interval=1, $count=200, $key='limit_default_key') {
        $this->_interval = $interval ? $interval : 1;
        $this->_count = $count;
        $this->_key = $key.'_'.intval(time()/$this->_interval);
        return $this;
    }
    
    /**
     * 判断访问次数，兼容缓存顺序依次为：apc, mem, redis
     */
    public function visit($interval=1, $count=200, $key='limit_default_key') {
        $this->_init($interval, $count, $key);
        $num = 0;
        if(($num=$this->_apc()) || ($num=$this->_mem()) || ($num=$this->_redis())){
            if($num > $this->_count){
                return false;
            }
            return true;
        }
        
        return null;
    } 

    /**
     * 获取apc缓存
     */
    private function _apc() {
        if(!function_exists('apc_inc')) { //apc可用
            return 0;
        }
        
        $ret = apc_inc($this->_key);
        if(!$ret) {
            apc_store($this->_key, 1, $this->_interval);
            $ret = 1;
        }
        return $ret;
    }

    /**
     * 获取memcached缓存
     */
    private function _mem() {
        $ret = 0;
        $mem = Cache_Memcached::get_instance();
        if($mem->enabled()) {
            $ret = $mem->increment($this->_key);
            if(!$ret) {
                $mem->set($this->_key, 1, $this->_interval);
                $ret = 1;
            }
        }
        return $ret;
    }

    /**
     * 获取redis缓存
     */
    private function _redis() {
        $ret = 0;
        $redis = Cache_Redis::get_instance();
        if($redis->enabled()) {
            $ret = $redis->incr($this->_key, 1);
            
            if(!$ret) {
                $redis->set($this->_key, 1, $this->_interval);
                $ret = 1;
            }
        }
        return $ret;
    }
    
    /**
     * 限制访问来源
     * @param array $host
     */
    public function referer($host) {
        $ret = "{'CODE':0, 'MSG':'无访问来源限制！'}";
        //为空时
        if(empty($host)) return $ret;
        
        $defaulthost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        
        //直接访问时,referer为空
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $defaulthost;
        
        //如果来源host是IP
        $type = $this->check_IP($referer) ? 1 : 2;
        
        //如果是域名
        if(!$this->check_allow_host($referer, $host, $type)) {
            $ret = "{'CODE':-1, 'MSG':'来源错误！'}";
            return $ret;
        }
        
        $ret = "{'CODE':1, 'MSG':'合法访问！'}";
        return $ret;
    }

    /**
     * 检测来源是否合法
     * @param array $host
     */    
    private function check_allow_host($referer, $host, $type=2) {
        $re_url = '/^(https?:\/\/)?([\w\.-]+)\.([a-z\.]{2,6})/';
        $re_ip = '/^(https?:\/\/)?(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/D';
        $re = $type == 1 ? $re_ip : $re_url;
        preg_match($re, $referer, $hostarr);

        $rehost = isset($hostarr[0]) ? $hostarr[0] : '';
        foreach($host as $value) {
            if(strpos($rehost, $value) !== false) return true;
        }
        return false;
    }
    
    /**
     * 检测IP是否合法
     * @param mixed $str    待检测字符串
     * @return bool
     */
    private function check_IP($str) {
        $str = preg_replace('/\.0+(\d+)/','.$1', $str);//去除前导0，如：112.15.042.45
        if (!preg_match('/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/D', $str)) {
            return false;
        }
        return true;
    }
}