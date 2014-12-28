<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午10:39
 */

class Process
{
    public $task;

    public function create_process($task)
    {
        $this->task = $task;
        $process = new swoole_process(array($this,"run"));
        $process->start();
    }

    public function autoload($class)
    {
        include(ROOT_PATH."plugin".DS."PluginBase.class.php");
        $file = ROOT_PATH."plugin".DS.$class.".class.php";
        if(file_exists($file)){
            include($file);
        }else{
            Main::log_write("处理类不存在");
        }
    }

    public function run($worker)
    {
        $class = $this->task["task"]["parse"];
        $worker->name("lzm_crontab_".$class."_".$this->task["id"]);
        $this->autoload($class);
        (new $class)->run($this->task["task"]);
    }
}

