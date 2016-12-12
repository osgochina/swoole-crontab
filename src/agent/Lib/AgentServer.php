<?php
/**
 * worker 服务
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午5:54
 */

namespace Lib;

class AgentServer extends SOAServer
{

    const REGISTER_TASKS = 0;//载入任务tasks进程
    const NOTIFY_TASKS = 1;//通知中心服

    public function onMasterStart($serv)
    {
        //连接中心服注册服务
        $this->register();
    }

    public function onWorkerStart($server, $worker_id)
    {
        if ($server->taskworker){
            if ($worker_id == (WORKER_NUM+self::REGISTER_TASKS)){
                //10秒钟发送一次信号给中心服，证明自己的存在
                $server->tick(10000, function () use ($server) {
                    $this->register();
                });
            }
            if ($worker_id == (WORKER_NUM+self::NOTIFY_TASKS)){
                //1秒判断一次任务执行状态，并通知主服务器
                $server->tick(1000, function () use ($server) {
                    //通知中心服
                    Process::notify();
                });
            }
        }else{
            Process::signal();//注册信号
        }
    }

    function onTask($serv, $task_id, $from_id, $data)
    {

    }
    function onFinish($serv, $task_id, $data)
    {

    }


    /**
     * 连接中心服注册服务
     */
    public function register()
    {
        $listenHost = Util::listenHost();
        $ret = Client::getInstance()->call("Agent::register",$listenHost,PORT)->getResult(3);
        if (empty($ret) || $ret["code"]){
            Flog::log($ret["msg"]);
            Client::removeInstance();
        }
    }

    public function call($request, $header)
    {
        //初始化日志
        Flog::log("call:".$request['call'].",params:".json_encode($request['params']));
        $ret =  parent::call($request, $header);
        Flog::log($ret);
        Flog::flush();
        return $ret;
    }
}