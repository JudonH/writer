<?php
/**
 * 驱动器预处理项目
 * @author tuyl
 *
 */
class Driver_PreLoad{

    /**
     * 在该函数中添加需要预处理的项目
     */
    private static function _pre_init(){
        //可以在这里注销$_GET等变量
        App_Plugin::add_action(App_Key::$TAG_SERVICE_EXECUTE, array('Driver_Input', 'init'));
        App_Plugin::add_action(App_Key::$TAG_SERVICE_EXECUTE, array('Driver_Cookie', 'init'));
        //App_Plugin::add_action(App_Key::$TAG_SERVICE_EXECUTE, array('Driver_Session', 'init'));
		App_Plugin::add_action(App_Key::$TAG_SERVICE_EXECUTE, array('Driver_Limit', 'init'));
    }
    
    /**
     * 预加载drive中的操作
     */
    public static function loading(){
        //防多次处理
        static $_loaded = false;
        if($_loaded) return ;
        
        //调用预加载项
        self::_pre_init();
        
        $_loaded = true;
    }
    
}