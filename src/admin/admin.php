<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 2017/11/15
 * Time: 16:10
 */

$swoole_version = \swoole_version();

if (version_compare($swoole_version,"1.9.17") == -1){
    exit("请升级swoole版本,最低版本需求1.9.17");
}
date_default_timezone_set("Asia/Shanghai");
define('WEBPATH', realpath(__DIR__));
define('APPSPATH', realpath(__DIR__));
$env = get_cfg_var('env.name');
if ($env == "product")
{
    define('DEBUG', 'off');
    define('WEBROOT', 'http://crontab.oa.com');
}elseif ($env == "test"){
    define('DEBUG', 'on');
    define('WEBROOT', 'http://crontab.oa.com');
} else {
    $env = 'dev';
    define('DEBUG', 'on');
    define('WEBROOT', 'http://crontab.oa.com');
}
define('ENV_NAME', $env);


define('PUBLIC_PATH', '/data/www/public/');
require_once PUBLIC_PATH . 'framework/libs/lib_config.php';

//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(__DIR__ . '/runtime/app_server.pid');

Swoole::$php->config->setPath(APPSPATH . '/configs');
Swoole::$php->config->setPath(APPSPATH . '/configs/' . ENV_NAME);

/**
 * 显示Usage界面
 * php app_server.php start|stop|reload
 */
Swoole\Network\Server::start(function ()
{
    $server = Swoole\Protocol\WebServer::create(__DIR__ . '/donkey.ini');
    $server->setAppPath(APPSPATH);                                 //设置应用所在的目录
    $server->setDocumentRoot(WEBPATH."/public");
    $server->setLogger(new \Swoole\Log\EchoLog(__DIR__ . "/runtime/donkey_admin.log")); //Logger

    //$server->daemonize();                                                  //作为守护进程
    $server->run(array('log_file' => __DIR__.'/runtime/php_errors.log'));
});