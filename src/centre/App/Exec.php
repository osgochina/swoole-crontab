<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午5:23
 */

namespace App;


use Lib\LoadTasks;
use Lib\Process;
use Lib\TermLog;

class Exec
{
    public static function run($task)
    {
        Process::create_process($task);
    }

    public static function notify($tasks)
    {
        if (empty($tasks) || count($tasks) <= 0){
            return ["code"=>101,"msg"=>"The tasks can't be empty"];
        }
        $header = LoadTasks::getTasks();
        foreach ($tasks as $task){
            if ($task["code"] == 0){
                $runStatus = LoadTasks::RunStatusSuccess;
            }else{
                $runStatus = LoadTasks::RunStatusFailed;
            }
            $header->set($task["taskId"],["runStatus"=>$runStatus,"runUpdateTime"=>microtime()]);
            TermLog::log("task已经执行完成,返回值:".json_encode($task),$task["taskId"]);
        }
        return ["code"=>0];
    }
}