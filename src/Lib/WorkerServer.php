<?php
/**
 * worker 服务
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午5:54
 */

namespace Lib;
use Swoole;

class WorkerServer extends Swoole\Protocol\SOAServer
{

    public function onMasterStart($serv)
    {
        //连接中心服注册服务
        $this->register();
    }

    public function onWorkerStart($server, $worker_id)
    {
        if (!$server->taskworker){
            Process::signal();//注册信号
            if ($worker_id == 0){
                //10秒钟发送一次信号给中心服，证明自己的存在
                $server->tick(10000, function () use ($server) {
                    $this->register();
                });
            }
            if ($worker_id == 1){
                //1秒判断一次任务执行状态，并通知主服务器
                $server->tick(1000, function () use ($server) {
                    $server->task("notify");
                });
            }
        }
    }

    function onTask($serv, $task_id, $from_id, $data)
    {
        if ($data == "notify"){
            //通知中心服
            Process::notify();
        }
        return true;
    }
    function onFinish($serv, $task_id, $data)
    {
        return;
    }


    /**
     * 连接中心服注册服务
     */
    public function register()
    {
        $listenHost = \Lib\Util::listenHost();
        $service = new Service();
        $ret = $service->call("Robot::register",$listenHost,PORT)->getResult();
        if (empty($ret) || $ret["code"]){
            Flog::log($ret["msg"]);
        }
        unset($service);
    }

    public function call($request, $header)
    {
        //初始化日志
        Flog::startLog($request['call']);
        Flog::log("call:".$request['call'].",params:".json_encode($request['params']));
        $ret =  parent::call($request, $header);
        Flog::log($ret);
        Flog::endLog();
        Flog::flush();
        return $ret;
    }
}