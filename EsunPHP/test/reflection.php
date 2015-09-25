<?php

class test{
    public function xx($i){
        echo $i;
    }
}


function xx1($i){
    $x = new ReflectionClass('test');
    $n = $x->newInstance();
    
    $m = $x->getMethod('xx');
    $m->invokeArgs($n, array($i));
}

function xx2($i){
    $x = new ReflectionClass('test');
    call_user_func_array(array($x->newInstance(), 'xx'), array($i));
}

function xx3($i){
    $xx = 'test';
    $x = new $xx;
    call_user_func_array(array($x, 'xx'), array($i));
}

function xx4($i){
    $xx = 'test';
    $x = new $xx;
    $m = new ReflectionMethod($x, 'xx');
    $m->invokeArgs($x, array($i));
}

function xx5($i){
    call_user_func_array(array('test', 'xx'), array($i));
}

for ($k=1; $k<6; $k++){
    $time1 = microtime(true);
    
    $n = 100;
    for($i=0; $i<$n; $i++){
        $x = 'xx'.$k;
        $x($i);
    }
    
    $time2 = microtime(true);
    
    echo '['.$x.']:'.($time2 - $time1).' | ';
}


