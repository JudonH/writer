<?php
/**
 * 内核用到的一些方法，禁止项目调用
 * @author tuyl
 *
 */
class Core_Utils{
    
    public static $forbidden_funcs = array('header', 'eval');
    
	/**
     * 获取一个字符串的随机串
     * @param string $name 需要重命名
     */
    public static function random_name($name){
        static $_name_list = array();
        !is_string($name) and $name = '_';
        if(!isset($_name_list[$name])){
            $_name_list[$name] = '_r_'.$name.'_'.rand(0, 9);
        }
        return $_name_list[$name];
    }
    
    /**
     * 异常格式化脚本
     * @param string|array $ex
     */
    public static function exception_handler($ex){
        if (is_string($ex) || (is_object($ex) && is_string($ex->__toString()))) {
            $trace          = debug_backtrace();
            $e['message']   = $ex;
            $e['file']      = $trace[0]['file'];
            $e['class']     = isset($trace[0]['class'])?$trace[0]['class']:'';
            $e['function']  = isset($trace[0]['function'])?$trace[0]['function']:'';
            $e['line']      = $trace[0]['line'];
            $trace_info      = '';
            $time = date('y-m-d H:i:m');
            foreach ($trace as $t) {
                $trace_info .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
                $trace_info .= $t['class'] . $t['type'] . $t['function'] . '(';
                $trace_info .= implode(', ', $t['args']);
                $trace_info .=')<br/>';
            }
            $e['trace']     = $trace_info;
        } else {
            $e              = $ex->__toString();
        }
        include App_Info::config('TEMPLATE_EXCEPTION');
    }
    
    public static function error_handler($err_no, $err_str, $err_file, $err_line){
        echo '【'.$err_no.'】'.$err_str;
    }
    
    public static function fatal_error_handler(){
        if ($e = error_get_last()) {
            self::error_handler($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }
    
}