<?php
/**
 * 
 * 日志类
 * @author chenzf@500wan.com
 * @example  
 *  $log = new Log_Log();
 *  $log->info('log');
 *  $log->info('log %s','test');
 */
class Log_File extends Log_Log{
        
    private $_file_path = '';   //日志存储路径    
    
    function __construct(){
        parent::__construct();
        $this->_file_path = $this->_config['log_file_path'];
        App_Plugin::add_action(App_Key::$TAG_SERVICE_STOP, array($this, 'writer'));
    }

    /**
     * 日志写文件
     * @throws App_Exception
     */
    public function writer(){
        if($this->_content=='') return true; 
        if (!$f = @fopen($this->_file_path, 'a', false)) {
           if(!file_exists(dirname($this->_file_path))) $this->_create_dir(dirname($this->_file_path));
           if (!$f = @fopen($this->_file_path, 'a', false)) {
                $msg = $this->_file_path.' count not open!';
                throw new App_Exception($msg);
           }
        }
        
        @fwrite($f, $this->_content);
        if (is_resource($f)) {
            @fclose($f);
        }
        return true;
    }
    
    /**
     * 建目录文件
     */
    private function _create_dir($path){
        if (!file_exists($path)){
            $this->_create_dir(dirname($path));
            @mkdir($path, 0755);
        }
    }
}              