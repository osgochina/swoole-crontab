<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午2:30
 */

require_once __DIR__ . '/_init.php';
const PORT = 8808;

Swoole\Network\Server::setPidFile(__DIR__ . '/logs/centre.pid');


Swoole\Network\Server::start(function ()
{
    Swoole\Loader::addNameSpace('App', __DIR__ . '/App');
    Swoole\Loader::addNameSpace('Lib', __DIR__ . '/Lib');
    
    $logger = new Swoole\Log\FileLog(['file' => __DIR__ . '/logs/centre.log']);
    $AppSvr = new Lib\CentreServer;
    $AppSvr->setLogger($logger);

    $setting = array(
        'worker_num' => 2,
        'task_worker_num'=>2,
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

    $listenHost = '127.0.0.1';
    if (ENV_NAME == 'product')
    {
        $iplist = swoole_get_local_ip();
        //监听局域网IP
        foreach ($iplist as $k => $v)
        {
            if (substr($v, 0, 7) == '192.168')
            {
                $listenHost = $v;
            }
        }
    }
    else if (ENV_NAME == 'test')
    {
        $iplist = swoole_get_local_ip();
        //监听局域网IP
        foreach ($iplist as $k => $v)
        {
            if (substr($v, 0, 6) == '172.16')
            {
                $listenHost = $v;
            }
        }
    }
    \Lib\LoadTasks::init();//载入任务表
    \Lib\Donkeyid::init();//初始化donkeyid对象
    \Lib\Tasks::init();//创建task表
    $server = Swoole\Network\Server::autoCreate($listenHost, PORT);
    $server->setProtocol($AppSvr);
    $server->setProcessName("CentreServer");
    $server->run($setting);
});

