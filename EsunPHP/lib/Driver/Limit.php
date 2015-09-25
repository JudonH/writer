<?php
/**
 *
 * 限制类
 * @author nathen
 * @date 2012-11-29
 */
class Driver_Limit{    
    
    private $_limit = null;   //限制类实例
    
    static public $over_limit = false;

    /**
     *
     * 构造函数
     * @param
     */
    function  __construct(){
        $this->_limit = new Limit_Visit();
    }

    /**
     * 初始化
     * @throws App_Exception
     * @return class
     */
    public static function init(){
        $f = App_Info::config('LIMIT_ENABLE');
        if(!$f) return ;
        
        $c = App_Info::config('LIMIT_CONFIG'); 
        $ins = new Limit_Visit();
        self::$over_limit = !$ins->visit($c['interval'], $c['count'], $c['key']);
    }
    
    /**
     *
     * 限制方法
     * @param
     * @return boolean
     */
    public function visit($interval=1, $count=100, $key=null){
        //决定一个页面
        !$key and $key=App_Info::$CONTROLLER_NAME.'/'.App_Info::$ACTION_NAME;
        return $this->_limit->visit($interval, $count, $key);
    }
    
    /**
     *
     * 限制访问来源
     * @param array $host
     * @return json
     */
    public function referer($host){        
        return $this->_limit->referer($host);
    }    
}