<?php

//自动加载类库操作
function esun_autoload($class_name){
    if(function_exists('ignore_autoload') && ignore_autoload($class_name)){
        return ;
    }
    include implode('/', explode('_', $class_name)) . '.php';
}
spl_autoload_register('esun_autoload');

//框架名称
defined('ESUN_PHP_NAME') or define('ESUN_PHP_NAME', 'EsunPHP');
//框架版本
define('ESUN_PHP_VERSION', '1.0');
//应用路径
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname($_SERVER['SCRIPT_FILENAME'])).'/');
defined('APP_PATH') or define('APP_PATH', '../');

//框架路径
defined('ESUN_PHP_PATH') or define('ESUN_PHP_PATH', dirname(__FILE__).'/');

set_include_path(get_include_path().PATH_SEPARATOR.ESUN_PHP_PATH.'lib/'.PATH_SEPARATOR.ROOT_PATH.'lib/');


//加载一些框架一定会用到的类库
include ESUN_PHP_PATH.'lib/App/Key.php';
include ESUN_PHP_PATH.'clib/Core/Exception.php';

include ESUN_PHP_PATH.'clib/Common/Func.php';
include ESUN_PHP_PATH.'clib/Common/Utils.php';
include ESUN_PHP_PATH.'clib/Core/Utils.php';
include ESUN_PHP_PATH.'clib/Core/Dispatcher.php';
include ESUN_PHP_PATH.'clib/Core/Action.php';
include ESUN_PHP_PATH.'clib/Core/Template.php';

include ESUN_PHP_PATH.'lib/App/Info.php';
include ESUN_PHP_PATH.'lib/App/Url.php';
include ESUN_PHP_PATH.'lib/App/Controller.php';
include ESUN_PHP_PATH.'lib/App/Exception.php';
include ESUN_PHP_PATH.'lib/App/Service.php';

//加载配置文件
App_Info::config(include ESUN_PHP_PATH.'inc/config.php');
