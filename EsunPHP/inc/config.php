<?php
return array(
    //url分隔符，如 /controller/action/key/value
    'URL_PATHINFO_SEPARATOR' => '/',
    //url结束扩展名 如 .html .htm 等
    'URL_SUFFIX' => '',
    //允许的url后缀扩展
    'URL_SUFFIX_ALLOW' => array('html', 'shtml', 'php'),
    
    //url是否开启路由，配合设置 URL_ROUTE_CONFIG一起使用
    'URL_ROUTE_ON' => false,
    //URL路由配置信息 
    //array('/^project\/(\d{2})\/(\d{5})$/' => 'Trade/project?wtype=:1&pid=:2', 
    //      '/^news\/(\d{2})$/' => array('http://www.500wan.com/news/:1', 301), //跳转，输出http状态码
    'URL_ROUTE_CONFIG' => array(),

    //允许调整的url域, 空则只有当前域
    'HOST_LOCATION_ALLOW' => array(),

    
    //默认从GET中获取C变量读入控制器名
    'PATH_CONTROLLER_VAR' => 'c',
    //默认从GET中获取a变量读入操作名
    'PATH_ACTION_VAR' => 'a',
    
    //默认为PATH形式的url
    'URL_TYPE' => App_Key::$URL_TYPE_COMMON,
    
    //默认的控制器名
    'DEFAULT_CONTROLLER_NAME' => 'home',
    //默认的操作名
    'DEFAULT_ACTION_NAME' => 'main',
    
    
    //模板目录
    'TEMPLATE_PATH' => ROOT_PATH.'view/',
    //模板的扩展名
    'TEMPLATE_EXT' => 'php',
    //模板类型
    'TEMPLATE_TYPE' =>App_Key::$TEMPLATE_PHPCODE,
    
    //控制器类目录
    'CONTROLLER_PATH' => ROOT_PATH.'controller/',
    //控制器名称扩展 如IndexController
    'CONTROLLER_NAME_EXT' => '',
    //操作名称扩展 如main_action
    'ACTION_NAME_EXT' => '',
    
    //禁止调用方法
    'FORBIDDEN_FUNCTIONS' => array(),
    
    'APP_CHARSET' => 'utf-8',
    //应用的标题
    'APP_TITLE'=> 'EsunPHP',
    //应用的关键字
    'APP_KEYWORDS'=> 'EsunPHP',
    //应用的关键字
    'APP_DESCRIPTION'=> 'EsunPHP Framework',

    //缓存类型
    'DEFAULT_CACHE_TYPE'        => App_Key::$CACHE_TYPE_MEMCACHED,
    //memcached连接配置
    'CACHE_MEM_CONFIG'  => array('default' => array('host'=>'192.168.41.101','port'=>'11211')),
    //redis连接配置
    'CACHE_REDIS_CONFIG'=> array('default' => array('host'=>'192.168.0.237','port'=>'6379')),
    
    //模版配置
    'TEMPLATE_CONFIG'=>array(
        //调用时需要加载的类库    
        'source_class'=>array(
            //smarty模版类库所在路径
            ESUN_PHP_PATH.'ext/Smarty/Smarty.class.php',
        ),
        'smarty_version'=>'3',
        //需要加载的配置
        'config'=>array(
            'template_dir'=> ROOT_PATH.'view/',//模版目录
            'compile_dir'=>ROOT_PATH.'tpl_cache/compile/',//编译文件目录
            'cache_dir'=>ROOT_PATH.'tpl_cache/cache/',//缓存文件目录
            'left_delimiter'=>'<!--{',//左分隔符
            'right_delimiter'=>'}-->',//右分隔符
            'caching'=>true,//是否开启缓存 
            'cache_lifetime'=>10,//缓存时间
        )
    ),
    
    //log配置
    'LOG_CONFIG'=>array(
        'log_type' => App_Key::$LOG_TYPE_FILE,//日志记录类型
        'log_record' => true,//是否保存
        'log_level' => App_Key::$LOG_LEVEL_INFO,//记录级别
        'log_length' => 0,//每条日志最大长度，0为没有最大长度
        'log_file_path'   => ROOT_PATH.'var/log/tem.log',//日志路径
    ),

    //limit配置
    'LIMIT_ENABLE' => false, //是否打开限制
    'LIMIT_CONFIG'=>array(
        'key' => 'Esun_Limit',//缓存key值
        'interval' => 10,//频率
        'count' => 200,//访问次数
    ),
    
    //XSS检查配置
    'XSS_CONFIG'=>array(
        'enabled' => true
    ),
    
    //Db配置
    'DB_CONFIG'  => array(
        'mysql_default' => array(
            'host'=>'127.0.0.1',
            'port'=>'3306',
            'user'=>'root',
            'pwd' => '123',
            'db'  => 'test',
            'charset' => 'utf8'
         )
    )
);
