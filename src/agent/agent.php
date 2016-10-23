<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午5:50
 */

include "_init.php";

Lib\Server::setPidFile(getRunPath() . '/logs/agent'.PORT.'.pid');
Lib\Server::start(function ()
{
    $logger = new Lib\FileLog(['file' => getRunPath() . '/logs/agent'.PORT.'.log']);
    $AppSvr = new Lib\AgentServer;
    $AppSvr->setLogger($logger);

    $setting = array(
        'worker_num' => WORKER_NUM,
        'task_worker_num'=>TASK_NUM,
        'max_request' => 1000,
        'dispatch_mode' => 3,
        'log_file' => getRunPath() . '/logs/swoole.log',
        'open_length_check' => 1,
        'package_max_length' => $AppSvr->packet_maxlen,
        'package_length_type' => 'N',
        'package_body_offset' => Lib\SOAServer::HEADER_SIZE,
        'package_length_offset' => 0,
    );
    //重定向PHP错误日志到logs目录
    ini_set('error_log', getRunPath() . '/logs/php_errors.log');

    $listenHost = Lib\Util::listenHost();
    
    Lib\Process::init();//载入任务处理表

    $server = Lib\Server::autoCreate($listenHost, PORT);
    $server->setProtocol($AppSvr);
    $server->setProcessName("AgentServer");
    $server->run($setting);
});
