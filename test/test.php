<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-2
 * Time: 下午10:15
 */
date_default_timezone_set('Asia/Shanghai');
swoole_timer_add(1000, function($interval) {
    while(true){
        if(timesleep() > 90 ){
            echo date("H:i:s")."\n";
            sleep(1);
            break;
        }
    }
});

function timesleep(){
    $rand = rand(0,100);
    echo $rand."\n";
    return $rand;
    if($rand >5){
        return false;
    }
    echo $rand."\n";
    sleep(1);
    return true;
}