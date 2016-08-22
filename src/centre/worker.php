<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午5:50
 */
require_once __DIR__ . '/_init.php';
const PORT = 8902;

Swoole\Network\Server::setPidFile(__DIR__ . '/logs/worker.pid');


Swoole\Network\Server::start(function ()
{

    $logger = new Swoole\Log\FileLog(['file' => __DIR__ . '/logs/worker.log']);
    $AppSvr = new Lib\WorkerServer;
    $AppSvr->setLogger($logger);

    $setting = array(
        'worker_num' => 4,
        'max_request' => 1000,
        'dispatch_mode' => 3,
        'log_file' => __DIR__ . '/logs/swoole.log',
        'open_length_check' => 1,
        'package_max_length' => $AppSvr->packet_maxlen,
        'package_length_type' => 'N',
        'package_body_offset' => \Swoole\Protocol\SOAServer::HEADER_SIZE,
        'package_length_offset' => 0,
    );
    //重定向PHP错误日志到logs目录
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');

    $listenHost = \Lib\Util::listenHost();
    
    \Lib\Process::init();//载入任务处理表

    $server = Swoole\Network\Server::autoCreate($listenHost, PORT);
    $server->setProtocol($AppSvr);
    $server->setProcessName("CentreServer");
    $server->run($setting);
});
