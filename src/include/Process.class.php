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

    /**
     * 创建一个子进程
     * @param $task
     */
    public function create_process($id,$task)
    {
        $this->task = $task;
        $process = new swoole_process(array($this, "run"));
        if(!($pid = $process->start())){

        }
        //记录当前任务
        Crontab::$task_list[$pid] = array(
            "start"=>microtime(true),
            "id"=>$id,
            "task"=>$task,
        );
    }

    /**
     * 子进程执行的入口
     * @param $worker
     */
    public function run($worker)
    {
        $class = $this->task["task"]["parse"];
        $worker->name("lzm_crontab_" . $class . "_" . $this->task["id"]);
        $this->autoload($class);
        (new $class)->run($this->task["task"]);
        self::_exit($worker);
    }

    private function _exit($worker)
    {
        $worker->exit(1);
    }

    /**
     * 子进程 自动载入需要运行的工作类
     * @param $class
     */
    public function autoload($class)
    {
        include(ROOT_PATH . "plugin" . DS . "PluginBase.class.php");
        $file = ROOT_PATH . "plugin" . DS . $class . ".class.php";
        if (file_exists($file)) {
            include($file);
        } else {
            Main::log_write("处理类不存在");
        }
    }
}

