<?php
class Core_Exception extends Exception{
    
    private $type = '';
    
    public function __construct($message, $code){
        $this->type = get_class($this);
        parent::__construct($message, $code);
    }
    
    public function get_info(){
        $trace = $this->getTrace();
        if(!isset($trace[0]['file']))
            array_shift($trace);
        //var_dump($trace);
        $this->class    =   isset($trace[0]['class'])?$trace[0]['class']:'';
        $this->function =   isset($trace[0]['function'])?$trace[0]['function']:'';
        $this->file     =   @$trace[0]['file'];
        $this->line     =   @$trace[0]['line'];
        $file           =   file($this->file);
        $trace_info      =   '';
        $time = date('y-m-d H:i:m');
        foreach($trace as $t) {
            if(!isset($t['file']) || !isset($t['file'])) continue;
            try{
            $xx = '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
            $xx .= $t['class'].$t['type'].$t['function'].'(';
            $xx .= @implode(', ', $t['args']);
            $xx .=")\n";
            $trace_info .= $xx;
            }catch (Exception $ex){}
        }
        $error['message']   = $this->message;
        $error['type']      = $this->type;
        $error['detail']    = '['.App_Info::$CONTROLLER_NAME.'] '.'['.App_Info::$ACTION_NAME.']'."\n";
        $error['detail']   .=   ($this->line-2).': '.$file[$this->line-3];
        $error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];
        $error['detail']   .=   '<font color="#FF6600" >'.($this->line).': <strong>'.$file[$this->line-1].'</strong></font>';
        $error['detail']   .=   ($this->line+1).': '.$file[$this->line];
        $error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
        $error['class']     =   $this->class;
        $error['function']  =   $this->function;
        $error['file']      = $this->file;
        $error['line']      = @$this->line;
        $error['trace']     = $trace_info;
        
        return $error;
    }
    
}