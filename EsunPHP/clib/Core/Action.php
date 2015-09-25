<?php
class Core_Action{
    
    private static function get_values_array($arr, $prx=''){
        $val = array();
        foreach ($arr as $k=>$v) {
            $k = is_int($k) ? '' : trim($k, '/').'/';
            $prxx = $prx.$k;
            if(is_array($v)){
                $valx = self::get_values_array($v, $prxx);
                $val = array_merge($valx);
            }else{
                $val[$v] = '/'.$prxx;
            }
        }
        return $val;
    }

    /**
     * 加载控制器
     * @param string $con_name
     */
    public static function import_controller($con_name, $con_ext){
        $con_path = App_Info::config('CONTROLLER_PATH');
        $con_ins = null;
        
        if(is_array($con_path)){
            $con_conf = self::get_values_array($con_path);
            if(!isset($con_conf[$con_name])){
                return $con_ins;
            }
            $con_path = $con_conf[$con_name];
        }
        $con_name = ucwords($con_name).$con_ext;
        $con_file = $con_path.$con_name.'.php';
        if (is_file($con_file)){
            require($con_file);
            if (class_exists($con_name)){
                $con_ins = new ReflectionClass($con_name);
            }
        }
        return $con_ins;
    }
    
    /**
     * 执行操作
     * @param string $con_ins
     * @param string $method
     * @param array $args
     */
    public static function exec_action($con_ins, $method, $get_args, $post_args){
        $controller = $con_ins->newInstance();
        $method_ins = $con_ins->getMethod($method);
        
        //绑定变量
        if($method_ins->getNumberOfParameters()>0){ 
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $vars    =  $post_args;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $vars);
                    break;
                default:
                    $vars  =  $get_args;
            }
            
            $args = array();
            $params =  $method_ins->getParameters();
            foreach ($params as $param){
                $name = $param->getName();
                if(isset($vars[$name])) {
                    $args[] =  $vars[$name];
                }elseif($param->isDefaultValueAvailable()){
                    $args[] = $param->getDefaultValue();
                }else{
                    throw new Core_Exception('Method Parameters Error', -102);
                }
            }
            
            $method_ins->invokeArgs($controller, $args);
        
        }else{
            $method_ins->invoke($controller);
        }
    }

}