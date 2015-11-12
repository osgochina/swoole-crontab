<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 15-11-4
 * Time: 下午9:39
 */
abstract class WorkerBase
{

    private  $Redis;
    private  $queue;
    protected $worker;
    private $ppid=0;

    public function content($config){

        if(!isset($config["host"]) || !isset($config["port"]) || !isset($config["timeout"]) || !isset($config["queue"])){
            Main::log_write(vsprintf(" host=%s,port=%s,timeout=%s,queue=%s",$config));
            exit;
        }

       $this->Redis = new Redis();
        if(!$this->Redis->pconnect($config["host"],$config["port"],isset($config["timeout"]))){
            Main::log_write(vsprintf("redis can't connect.host=%s,port=%s,timeout=%s",$config));
            exit;
        }
        if(isset($config["db"]) && is_numeric($config["db"])){
            $this->Redis->select($config["db"]);
        }
        $this->queue = $config["queue"];
    }

    public function getQueue(){
        return $this->Redis->rpop($this->queue);
    }
    public function tick($worker){
        $this->worker = $worker;
        swoole_timer_add(500, function() {
            $this->checkExit();
            while(true){
                $task = $this->getQueue();
                if(empty($task)){
                    break;
                }
                $this->Run($task);
            }
        });
    }
    protected function _exit()
    {
        $this->worker->exit(1);
    }

    /**
     * 判断父进程是否结束
     */
    private function checkExit(){
        $ppid = posix_getppid();
        if($this->ppid == 0){
            $this->ppid = $ppid ;
        }
        if($this->ppid != $ppid){
            $this->_exit();
        }
    }

    /**
     * 运行入口
     * @param $task
     * @return mixed
     */
    abstract public function Run($task);



}