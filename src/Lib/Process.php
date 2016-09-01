<?php
/**
 * worker服务中  新创建一个进程去执行命令
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午6:04
 */

namespace Lib;


class Process
{
    private static $table;
    static private $column = [
        "taskId" => [\swoole_table::TYPE_INT, 8],
        "status" => [\swoole_table::TYPE_INT, 1],
        "start" => [\swoole_table::TYPE_INT, 8],
        "end" => [\swoole_table::TYPE_INT, 8],
        "code"=> [\swoole_table::TYPE_INT, 1],
    ];
    const PROCESS_START = 0;//程序开始运行
    const PROCESS_STOP = 1;//程序结束运行

    public $task;

    public static function init()
    {
        $robot_process_max = defined("ROBOT_MAX_PROCESS") ? ROBOT_MAX_PROCESS : 128;
        self::$table = new \swoole_table($robot_process_max);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();
    }

    /**
     * 注册信号
     */
    public static function signal()
    {
        \swoole_process::signal(SIGCHLD, function($sig) {
            //必须为false，非阻塞模式
            while($ret =  \swoole_process::wait(false)) {
                $pid = $ret['pid'];
                if (self::$table->exist($pid)){
                    self::$table->set($pid,["status"=>self::PROCESS_STOP,"end"=>microtime(true),"code"=>$ret["code"]]);
                    $task = self::$table->get($pid);
                    TermLog::log("task执行完成:".json_encode($task),$task["taskId"]);
                }
            }
        });
    }

    /**
     * 通知中心任务执行结果
     * @return bool
     */
    public static function notify()
    {
        if (count(self::$table) >0){
            $procs= [];
            foreach (self::$table as $pid=>$process){
                if ($process["status"] == self::PROCESS_STOP){
                    $procs[$pid] = [
                        "taskId"=>$process["taskId"],
                        "start"=>$process["start"],
                        "end"=>$process["end"],
                        "code"=>$process["code"],
                    ];
                }
            }
            TermLog::log("tasks通知中心服:".json_encode($procs));
            $service = new Service();
            $rect = $service->call("Exec::notify",$procs);
            $ret = $rect->getResult(1);
            unset($service);
            if (empty($ret)){
                TermLog::log("tasks通知中心服失败,code".$rect->code.",msg".$rect->msg);
                return false;
            }

            foreach ($procs as $pid=>$v){
                self::$table->del($pid);
            }
        }
        return true;
    }

    /**
     * 创建一个子进程
     * @param $task
     * @return bool
     */
    public static function create_process($task)
    {
        $cls = new self();
        $cls->task = $task;
        $process = new \swoole_process(array($cls, "run"));
        if (($pid = $process->start())) {
            TermLog::log("task开始执行:".json_encode($task),$task["id"]);
            self::$table->set($pid,["taskId"=>$task["id"],"status"=>self::PROCESS_START,"start"=>microtime(true)]);
            return true;
        }
        return false;
    }

    /**
     * 子进程执行的入口
     * @param $worker
     */
    public function run($worker)
    {
        $exec = $this->task["execute"];
        $worker->name($exec ."#". $this->task["id"]);
        $exec = explode(" ",$exec);
        $execfile = $exec[0];
        unset($exec[0]);
        $worker->exec($execfile,$exec);
    }
}