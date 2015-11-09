<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午3:13
 */
abstract class PluginBase
{
    public $worker;

    public function delay($sec){
        if(!is_numeric($sec)){
            return false;
        }
        $task = $this->worker->pid.",".$sec;
        $this->worker->write($task);
        if($this->worker->read() ==$task){
            return true;
        }
        return false;
    }

    abstract public function run($task);


}