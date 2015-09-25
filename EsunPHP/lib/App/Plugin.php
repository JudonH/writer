<?php 
class App_Plugin{
    
    private static $_actions = array();
    
    /**
     * 添加插件
     * @param string $tag
     * @param string $fun function_name或者 array('class', 'method')
     * @param int $priority 优先级
     */
    public static function add_action($tag, $action, $priority=10){
        $idx = self::get_unique_id($tag, $action, $priority);
        self::$_actions[$tag][$priority][$idx] = array('action' => $action);
    }
	
	
	/**
     * 移走插件
     * @param string $tag
     * @param string $fun function_name或者 array('class', 'method')
     * @param int $priority 优先级
     */
    public static function remove_action($tag, $action, $priority=10){
        $idx = self::get_unique_id($tag, $action, $priority);
        unset(self::$_actions[$tag][$priority][$idx]);
    }
	
    
    /**
     * 注册HOOK
     * @param string $tag
     * @param array $arg
     * @return boolen
     */
    public static function do_action($tag, &$args = ''){
        if(!isset(self::$_actions[$tag]) || empty(self::$_actions[$tag])){ //没有插件执行
            return false;
        }
        
        ksort(self::$_actions[$tag]); //按优先级排序
        foreach (self::$_actions[$tag] as $acts){
            foreach($acts as $act){
                if(is_string($act['action']) && function_exists($act['action'])){
                    call_user_func_array($act['action'], $args);
                }elseif(is_array($act['action']) && count($act['action'])==2){
                    call_user_func(array($act['action'][0], $act['action'][1]), $args);
                }
            }
        }
        return true;
    }
    
    /**
     * 获取唯一key
     * @param string $tag
     * @param string $fun
     * @param int $priority
     */
    private static function get_unique_id($tag, $action, $priority){
        if(is_array($action)) $action = (is_object($action[0]) ? get_class($action[0]) : $action[0]).$action[1];
        return sprintf('%s_%s_%s', $tag, $action, $priority);
    }
}
