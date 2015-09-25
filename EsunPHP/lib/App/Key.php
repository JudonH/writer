<?php

class App_Key{
    //cache类型
    public static $CACHE_TYPE_MEMCACHED =  'memcached';
    public static $CACHE_TYPE_APC       =  'apc';
    public static $CACHE_TYPE_REDIS     =  'redis';
    
    //url路径模式 如:controller/action/key/value
    public static $URL_TYPE_PATH = 1;
    
    //url组合模式 如:?c=controller&a=action&key=value
    public static $URL_TYPE_COMMON = 2;
    
    public static $ERROR_KEY              = -1000;
    
    public static $CONTROLLER_NOT_EXTENTD = -1001;
    public static $CONTROLLER_NOT_EXIST   = -1002;
    public static $ACTION_NOT_EXIST       = -1003;
    public static $ACTION_NAME_ERROR      = -1004;
    public static $ERR_NOT_METHOD_DEFINED = -1011;
    public static $ERR_NOT_DRIVER_DEFINED = -1012;
    public static $ERR_XSS_CODE_FOUND     = -1013;
    
    public static $ERR_DB                 = -2001;

    public static $TEMPLATE_SMARTY='Smarty';
    public static $TEMPLATE_PHPCODE='PHPCode';
    public static $ERR_NOT_DEFINE_TEMPLATE=-104; //未找到相应的模版引擎
    
    //log类型
    public static $LOG_TYPE_FILE        =  'file';
    public static $LOG_LEVEL_DEBUG      =  'DEBUG';
    public static $LOG_LEVEL_INFO       =  'INFO';
    public static $LOG_LEVEL_ERROR      =  'ERROR';
    public static $LOG_LEVELS           =  array('DEBUG'=>1,'INFO'=>2,'ERROR'=>3);
    
    //EsunPHP插件的tag节点
    public static $TAG_SERVICE_START    = 100;
    public static $TAG_SERVICE_EXECUTE  = 101;
    public static $TAG_SERVICE_STOP     = 102;
    
}

