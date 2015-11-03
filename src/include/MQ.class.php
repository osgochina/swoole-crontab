<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-3
 * Time: 下午10:08
 */
class MQ
{

    private $redis;
    private $queue;

    public function connect($config){
        $this->redis = new Redis();
        $this->redis->pconnect($config["host"],$config["port"],$config["timeout"]);
        $this->queue = $config["queue"];

    }

    public function getQueue(){
        return $this->redis->rpop($this->queue);
    }

    public function tick(){
        swoole_timer_add(500, function($interval) {
            echo "timer[$interval] :".date("H:i:s")." call\n";
        });
    }

    public function Run(){

    }

}