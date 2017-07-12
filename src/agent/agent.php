<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 2017/7/11
 * Time: 13:22
 */
define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);
date_default_timezone_set("Asia/Shanghai");
require WEBPATH . "/Lib/Loader.php";
spl_autoload_register('\\Lib\\Loader::autoload');
Lib\Loader::addNameSpace('Lib', WEBPATH."/Lib");
Lib\Loader::addNameSpace('App', WEBPATH."/App");

const ROBOT_MAX_PROCESS = 1024;//单个worker同时执行任务数量
Lib\Server::setPidFile('/data/logs/agent.pid');
Lib\Server::start(function ($opt)
{
    $AppSvr = new Lib\Agent;
    $setting = array(
        'open_length_check' => 1,
        'package_max_length' => 2465792,
        'package_length_type' => 'N',
        'package_body_offset' => Lib\SOAProtocol::HEADER_SIZE,
        'package_length_offset' => 0
    );
    $host = isset($opt["host"])?$opt["host"]:"127.0.0.1";
    $port = isset($opt["port"])?$opt["port"]:8901;

    Lib\Process::signal();//注册信号
    //5秒同步一次日志
    swoole_timer_tick(5000,function (){
        Lib\Process::notify();
    });
    $server = Lib\Server::autoCreate($host,$port);
    $server->setServer($AppSvr);
    $server->setProcessName("AgentServer");
    $server->run($setting);
});