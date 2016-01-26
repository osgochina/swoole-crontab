<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 15-11-4
 * Time: 下午10:02
 */
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
        if($task == "exit"){
            $this->_exit(2);
        }
    }
}