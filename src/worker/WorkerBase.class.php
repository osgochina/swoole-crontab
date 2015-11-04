<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 15-11-4
 * Time: 下午9:39
 */
abstract class WorkerBase
{

    private static $Redis;
    private static $queue;

    public function content($config){
        if(!isset($config["host"]) || !isset($config["port"]) || !isset($config["timeout"]) || !isset($config["queue"])){
            Main::log_write(vsprintf(" host=%s,port=%s,timeout=%s,queue=%s",$config));
            exit;
        }
        self::$Redis = new Redis();
        if(!self::$Redis->pconnect($config["host"],$config["port"],isset($config["timeout"]))){
            Main::log_write(vsprintf("redis can't connect.host=%s,port=%s,timeout=%s",$config));
            exit;
        }
        if(isset($config["db"]) && is_numeric($config["db"])){
            self::$Redis->select($config["db"]);
        }
        self::$queue = $config["queue"];
    }

    public function getQueue(){
        return self::$Redis->rpop(self::$queue);
    }

    public function tick(){
        swoole_timer_add(500, function() {
            while(true){
                $task = $this->getQueue();
                if(empty($task)){
                    break;
                }
                $this->Run($task);
            }

        });
    }

    /**
     * 运行入口
     * @param $task
     * @return mixed
     */
    abstract public function Run($task);

}