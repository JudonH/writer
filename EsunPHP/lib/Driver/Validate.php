<?php
class Driver_Validate{
    
    /**
     * 检查是否只有数字或字母字符
     * @param mixed $val
     */
    public static function is_alnum($val){
        if (!is_string($val) && !is_int($val) && !is_float($val)) {
            return false;
        }
        $pattern = '/[^a-zA-Z0-9]/';
        return $val === preg_replace($pattern, '', (string)$val);
    }
    
    /**
     * 检查是否只有字母字符
     * @param mixed $val
     */
    public static function is_alpha($val){
        if(!is_string($val)){
            return false;
        }
        
        $pattern = '/[^a-zA-Z]/';
        return $val === preg_replace($pattern, '', (string)$val);
    }
    
    /**
     * 判断值在最小值和最大值之间
     * @param mixed $val
     * @param mixed $min
     * @param mixed $max
     */
    public static function is_between($val, $min, $max){
        if($val>$max || $val<$min){
            return false;
        }
        return true;
    }
    
    /**
     * 检查字符串长度合法性
     * @param string $val
     * @param int $min
     * @param int $max
     */
    public static function is_strlen($val, $min, $max){
        return self::is_between(strlen($val), $min, $max);
    }
    
    /**
     * 检查是否是时间类型
     * @param string $val
     * @param string $format
     */
    public static function is_date($val, $format=null){
        if($format){
            return strptime($val, $format) ? true : false;
        }
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $val)){
            return false;
        }
        
        list($year, $month, $day) = sscanf($val, '%d-%d-%d');
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        return true;
    }
    
    /**
     * 是否只包含数字字符
     * @param mixed $val
     */
    public static function is_digits($val){
        if(!is_string($val) && !is_int($val) && !is_float($val)) {
            return false;
        }
        $pattern = '/[\p{^N}]/';
        return $val === preg_replace($pattern, '', (string)$val);
    }
    
    /**
     * 检查是否email，PHP版本5.2以后
     * @param string $val
     */
    public static function is_email($val){
        if(PHP_VERSION >= '5.2'){
            return filter_var($val, FILTER_VALIDATE_EMAIL);
        }
        $valid = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/D", $val);
        return $valid ? true : false;
    }
    
    /**
     * 检查ip合法性
     * @param string $val
     */
    public static function is_ip($val){
        if(PHP_VERSION >= '5.2'){
            return filter_var($val, FILTER_VALIDATE_IP);
        }
        $valid = preg_match('/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/D', $val);
        return $valid ? true : false;
    }
    
    /**
     * 检查url合法性
     * @param string $val
     */
    public static function is_url($val){
        if(PHP_VERSION >= '5.2'){
            return filter_var($val, FILTER_VALIDATE_URL);
        }
        $valid = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|Di', $val);
        return $valid ? true : false;
    }
    
    public function is_valid($filter_arr, $val){
        $ret = true;
        foreach ($filter_arr as $func=>$args) {
            if(is_int($func)){
                $func =  $args;
                $args = array();
            }
            array_unshift($args, $val);
            $retx = call_user_func_array(array($this, 'is_'.$func), $args);
            $ret = $retx && $ret;
        }
        return $ret;
    }
}