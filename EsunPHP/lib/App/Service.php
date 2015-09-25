<?php

class App_Service{
    private static $_is_init = false;
    
    /**
     * 禁止系统调用的函数
     */
    private static function _init_funtions(){
        if(!function_exists('rename_function')) return ;
        $funcs = array_merge(App_Info::config('FORBIDDEN_FUNCTIONS'), Core_Utils::$forbidden_funcs);
        foreach ($funcs as $func) {
            if(function_exists($func)){
                $funcx = Core_Utils::random_name($func);
                rename_function($func, $funcx);
            }
        }
    }
    
    /**
     * xss检查
     */
    private static function _xss_check() {
        $xss_config = App_Info::config('XSS_CONFIG');
        if(isset($xss_config['enabled']) && $xss_config['enabled']){
            $temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
            if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
                 throw new Core_Exception('XSS Code Found!', App_Key::$ERR_XSS_CODE_FOUND);
            }
            return true;
        }
        return false;
    }
    
    /**
     * 异常处理初始化
     */
    private static function _init_exception(){
        set_exception_handler(array('Core_Utils', 'exception_handler'));
        set_error_handler(array('Core_Utils','error_handler'));
        register_shutdown_function(array('Core_Utils','fatal_error_handler'));
    }
    
    /**
     * 初始化APP Service
     */
    private static function _init(){
        //xss检查
        self::_xss_check();
        
        //初始化驱动器相关加载
        Driver_PreLoad::loading();
        
        //url初始化
        Core_Dispatcher::init();
        
        //控制器，操作符分析
        Core_Dispatcher::dispatch();
    }
    
    /**
     * 针对控制器不存在，但存在模板的操作
     */
    private static function _load_default_template(){
        $temp = Common_Func::get_instance('Core_Template');
        $temp_file = $temp->template_view()->template_file();
        if(file_exists($temp_file)){
            $temp->render();
            return true;
        }
        return false;
    }
    
    /**
     * 执行APP Service调用
     */
    private static function _exec($get_args, $post_args){
        $c = App_Info::$CONTROLLER_NAME;
        $a = App_Info::$ACTION_NAME;
        
        $con_ins = false;
        $con_ext = App_Info::config('CONTROLLER_NAME_EXT');
        if(preg_match('/^[A-Za-z](\w)*$/D', $c)){ //检测控制器名安全
            $con_ins = Core_Action::import_controller($c, $con_ext);
        }
        
        if (!$con_ins){ //如果控制器不存在，试图访问空控制器
            $con_ins = Core_Action::import_controller('empty', $con_ext);
        }
        
        $method_exist = false;
        if($con_ins){
            if(!$con_ins->isSubclassOf('App_Controller')){
                throw new Core_Exception('Controller['.$c.'] Don\'t extends App_Controller', App_Key::$CONTROLLER_NOT_EXTENTD);
            }
            
            if(!preg_match('/^[A-Za-z](\w)*$/D',$a)){ //检测操作名安全
                throw new Core_Exception('Action Name['.htmlspecialchars($a).'] Error, Controller:'.$c, App_Key::$ACTION_NAME_ERROR);
            }
            
            $act_ext = App_Info::config('ACTION_NAME_EXT');
            $method_name = $a.$act_ext;
            
            if($con_ins->hasMethod($method_name)){//操作存在，执行
                $method_exist = true;
                Core_Action::exec_action($con_ins, $method_name, $get_args, $post_args);
            }else{
                $method_name = 'undefined'.$act_ext;
                if($con_ins->hasMethod($method_name)) { //empty操作存在，执行
                    $method_exist = true;
                    Core_Action::exec_action($con_ins, $method_name, $get_args, $post_args);
                }
            }
        }
        
        if(!$con_ins || !$method_exist){
            $f = self::_load_default_template();
            $g = $c === App_Info::config('DEFAULT_CONTROLLER_NAME')
                 && $a === App_Info::config('DEFAULT_ACTION_NAME');
            if(!$f){
                if($g){ //新系统
                    include ESUN_PHP_PATH.'view/index/main.php';
                }else{
                    if($con_ins){ //控制器存在
                        throw new Core_Exception('Action Name['.htmlspecialchars($a).'] Error, Controller:'.$c, App_Key::$ACTION_NOT_EXIST);
                    }else{
                        throw new Core_Exception('Controller Name['.htmlspecialchars($c).'] Error', App_Key::$CONTROLLER_NOT_EXIST);
                        //Common_Func::send_http_status(404);
                    }
                }
            }
        }
    }
    
    /**
     * 初始化，格式化url等
     */
    public static function init(){
        if(self::$_is_init) return ;
        App_Plugin::do_action(App_Key::$TAG_SERVICE_START);
        self::_init();
        self::$_is_init = true;
    }
    
    /**
     * 启动服务
     */
    public static function start(){
        self::init();
        $get_args = $_GET; $post_args = $_POST;
        App_Plugin::do_action(App_Key::$TAG_SERVICE_EXECUTE);
        self::_exec($get_args, $post_args);
        App_Plugin::do_action(App_Key::$TAG_SERVICE_STOP);
    }
    
    /**
     * 启用插件
     * @param string $plugin 插件模块类名
     */
    public static function setup_plugin($plugin){
        if(is_array($plugin)){
            foreach ($plugin as $v) {
                $ins = Common_Func::get_instance($v);
                $ins->setup();
            }
        }else{
            $ins = Common_Func::get_instance($plugin);
            $ins->setup();
        }
    }
}

