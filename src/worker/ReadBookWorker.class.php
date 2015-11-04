<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-4
 * Time: 下午10:02
 */
include "./WorkerBase.class.php";
class ReadBookWorker extends WorkerBase
{

    /**
     * 运行入口
     * @param $task
     * @return mixed
     */
    public function Run($task)
    {
        echo $task."\n";
    }
}

$readbook = new ReadBookWorker();
$readbook->content(array("host"=>"127.0.0.1","port"=>6379,"timeout"=>30,"queue"=>"test"));
$readbook->tick();