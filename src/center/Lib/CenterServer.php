<?php
/**
 * 中心服服务
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午3:56
 */

namespace Lib;

use Swoole;

class CenterServer extends Swoole\Protocol\SOAServer
{
    /**
     * @var Swoole\Network\Server
     */
    public static $_server;

    const LOAD_TASKS = 0;//载入任务tasks进程
    const GET_TASKS = 1;//获取到期task进程
    const EXEC_TASKS = 2;//执行task
    const MANAGER_TASKS = 3;//管理task状态

    function onWorkerStart($server, $worker_id)
    {
        Swoole::$php->db->connect();
        if ($server->taskworker) {
            if ($worker_id == (WORKER_NUM + self::LOAD_TASKS)) {
                //准点载入任务
                $server->after((60 - date("s")) * 1000, function () use ($server) {
                    Tasks::checkTasks();
                    $server->tick(60000, function () use ($server) {
                        Tasks::checkTasks();
                    });
                });
            }
            if ($worker_id == WORKER_NUM + self::GET_TASKS) {
                $server->tick(500, function () use ($server) {
                    $tasks = Tasks::getTasks();
                    if (!empty($tasks)) {
                        $server->sendMessage(json_encode($tasks), (WORKER_NUM + self::EXEC_TASKS));
                    }
                });
            }
        }
    }

    public function onTask()
    {

    }

    public function onFinish()
    {
    }

    public function onPipeMessage($serv, $src_worker_id, $data)
    {
        $data = json_decode($data, true);
        $loadtasks = LoadTasks::getTasks();
        if ($src_worker_id == WORKER_NUM + self::GET_TASKS) {
            $ret = [];
            foreach ($data as $k => $id) {
                $task = $loadtasks->get($id);
                //限制任务多次执行，保证同时只有符合数量的任务运行。如果限制条件为0，则不限制数量
                if ($task["runnumber"] > 0 && $task["execNum"] >= $task["runnumber"]) {
                    $loadtasks->set($id, ["runStatus" => LoadTasks::RunStatusError]);
                    if (Tasks::$table->exist($k)) {
                        Tasks::$table->set($k, ["runStatus" => LoadTasks::RunStatusError, "runid" => $k]);
                    }
                    TermLog::log($k, $id, "并发任务超限", $task);
                    continue;
                }
                $tmp["id"] = $id;
                $tmp["execute"] = $task["execute"];
                $tmp["agents"] = $task["agents"];
                $tmp["taskname"] = $task["taskname"];
                $tmp["runuser"] = $task["runuser"];
                $tmp["timeout"] = $task["timeout"];
                $tmp["runid"] = $k;
                //正在运行标示
                if (Tasks::$table->exist($k)) {
                    Tasks::$table->set($k, ["runStatus" => LoadTasks::RunStatusStart, "runid" => $k]);
                }
                TermLog::log($tmp["runid"], $id, "任务开始", $tmp);
                $ret[$k] = [
                    "id" => $id,
                    "ret" => Robot::Run($tmp)
                ];
            }
            $serv->sendMessage(json_encode($ret), WORKER_NUM + self::MANAGER_TASKS);
        } else {
            if ($src_worker_id == WORKER_NUM + self::EXEC_TASKS) {
                foreach ($data as $k => $v) {
                    if ($v["ret"]) {
                        $loadtasks->incr($v["id"], 'execNum');//增加当前执行数量
                        $runStatus = LoadTasks::RunStatusToTaskSuccess;//发送成功
                        TermLog::log($k, $v["id"], "任务发送成功");
                    } else {
                        $runStatus = LoadTasks::RunStatusToTaskFailed;//发送失败
                        TermLog::log($k, $v["id"], "任务发送失败");
                        Report::taskSendFailed($v["id"], $k);//报警
                    }
                    $loadtasks->set($v["id"], ["runStatus" => $runStatus, "runUpdateTime" => time()]);
                    if (Tasks::$table->exist($k)) {
                        Tasks::$table->set($k, ["runStatus" => $runStatus]);
                    }
                }
            }
        }
    }

    public function call($request, $header)
    {
        //初始化日志
        Flog::startLog($request['call']);
        Flog::log("call:" . $request['call'] . ",params:" . json_encode($request['params']));
        if ($request['call'] == 'register') {
            if (Robot::register($header['fd'], self::$clientEnv['_socket']['remote_ip'])) {
                return array('errno' => 0, 'data' => Util::errCodeMsg(0, "注册成功"));
            }
        }
        $ret = parent::call($request, $header);
        Flog::log($ret);
        Flog::endLog();
        Flog::flush();
        return $ret;
    }

    public function onClose($serv, $fd, $from_id)
    {
        parent::onClose($serv, $fd, $from_id);
        Robot::unRegister($fd);
    }
}