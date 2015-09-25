<?php
class Common_Func{
    /**
     * 获取server环境变量
     */
    public static function env($key, $default=null){
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }
    
    /**
     * 获取对象单例模式
     * @param string $class_name
     */
    public static function get_instance($class_name){
        static $_instance = array();
        if($class_name && !isset($_instance[$class_name])){
            $_instance[$class_name] = new $class_name();
        }
        
        return $_instance[$class_name];
    }
    
    /**
     * 获取对象单例模式
     * @param string $class_name
     */
    public static function get_instance_array($class_name, $params){
        static $_instance = array();
        $key = @serialize($params).'*'.$class_name;
        if($class_name && !isset($_instance[$key])){
            $cls = new ReflectionClass($class_name);
            $ins = $cls->newInstanceArgs($params);
            $_instance[$key] = $ins;
        }
        return $_instance[$key];
    }
    
    public static function url_in_array_host($url, $host_arr){
        $urlinfo = parse_url($url);
        if(!isset($urlinfo['host'])) return null;
        
        $host = $urlinfo['host'];
        foreach ($host_arr as $hosx) {
            $last = strrpos($host, $hosx);
            if(substr($host, $last) == $hosx){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 发送HTTP头信息
     * @param integer $code 状态码
     * @return void
     */
    public static function send_http_status($code) {
        if(headers_sent()) return ;
        
        static $_status = array(
            200 => 'OK',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',
            400 => 'Bad Request',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
            header('Status:'.$code.' '.$_status[$code]);
        }
    }
    
    public static function get_microtime(){
        list($usec, $sec) = explode(' ', microtime()); 
        return floatval($sec) + floatval($usec);
    }
    
    /**
     * 获取客户端ip
     */
    public static function get_ip() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])){
                $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])){
                $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])){
                $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
                $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        if($ip == '127.0.0.1'){
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
   		$ips =  preg_split('/,|\s+/', $ip);       
		$ip  =  isset($ips[0])?$ips[0]:"";
        $pattern = "/^(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])$/D";
        if (!preg_match($pattern, $ip)){
            return "";
        }
        return $ip;
    }
    
    /**
     * 检查来访是否是手机客户端
     */
    public static function is_mobile(){
        switch(true){
            // Apple/iPhone browser renders as mobile
            case (preg_match('/(apple|iphone|ipod)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/mobile/i', $_SERVER['HTTP_USER_AGENT'])):
                return true;
                break;
                
            // Other mobile browsers render as mobile
            case (preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera     mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i',$_SERVER['HTTP_USER_AGENT'])):
                return true;
                break;
            
            // Wap browser
            case (isset($_SERVER['HTTP_ACCEPT']) && (((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'text/vnd.wap.wml') > 0) || (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0)) || ((isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))))):
                return true;
                break;
                
            // Shortend user agents
            case (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,3)),array('lg '=>'lg ','lg-'=>'lg-','lg_'=>'lg_','lge'=>'lge')));
                return true;
                break;
                
            // More shortend user agents    
            case(in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)),array('acs-'=>'acs-','amoi'=>'amoi','doco'=>'doco','eric'=>'eric','huaw'=>'huaw','lct_'=>'lct_','leno'=>'leno','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','nec-'=>'nec-','phil'=>'phil','sams'=>'sams','sch-'=>'sch-','shar'=>'shar','sie-'=>'sie-','wap_'=>'wap_','zte-'=>'zte-')));
                return true;
                break;
            
            // Render mobile site for mobile search engines
            case (preg_match('/Googlebot-Mobile/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/YahooSeeker\/M1A1-R2D2/i', $_SERVER['HTTP_USER_AGENT'])):
                return true;
                break;
        }
        return false;
    }
    
    /**
     * 基于PHP的 mb_substr，iconv_substr 这两个扩展来截取字符串，中文字符都是按1个字符长度计算；
     * 该函数仅适用于utf-8编码的中文字符串。
     * 
     * @param  $str      原始字符串
     * @param  $len   截取的字符数
     * @param  $append   替换截掉部分的结尾字符串
     * @return 返回截取后的字符串
     */
    public static function substr($str, $start=0, $len=0, $append = '...') {
        $strlen = strlen($str);

        if($len<=0 || $len>=$strlen){
            $len = $strlen;
        }

        if(function_exists('mb_substr')){
            $newstr = mb_substr($str, $start, $len, 'utf-8');
        }elseif(function_exists('iconv_substr')){
            $newstr = iconv_substr($str, $start, $len, 'utf-8');
        }else{
            $newstr = substr($str, $start, $len);
        }
        
        if ($append && $str!=$newstr){
            $newstr .= $append;
        }
        
        return $newstr;
    }
    
    /**
     * js弹出对话框代码生成器
     * 
     * @param  $msg      需要提示的信息
     * @param  $url      提示后跳转url
     * @param  $return   是否需要返回html代码
     */
    public static function js_alert($msg, $url='', $return=false){
        $html = <<<EOD
        <script>
            var url="$url";
            alert("$msg");
            
            if(url != ''){
                document.location.href = url;
            }else{
                if(history.length==0){
                    window.opener = '';
                    window.close();
                }else{
                    history.go(-1);
                }

            }
        </script>
EOD;
        
        if(!$return){
            echo $html;
            exit();
        }
    }
    
}