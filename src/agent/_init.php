<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-10-9
 * Time: 下午2:07
 */

define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);

const ROBOT_MAX_PROCESS = 128;//单个worker同时执行任务数量
const CENTER_PORT = 8901;//中心端口号
const WORKER_NUM = 2;//worker进程数量
const TASK_NUM = 2;//task进程数量

require WEBPATH."/Lib/Loader.php";
Lib\Loader::addNameSpace('Lib', __DIR__."/Lib");
Lib\Loader::addNameSpace('App', __DIR__."/App");
spl_autoload_register('\\Lib\\Loader::autoload');


$env = get_cfg_var('env.name');
if (empty($env))
{
    $env = 'product';
    define('DEBUG', 'off');
    define("CENTER_HOST","192.168.1.244");
}
else
{
    define('DEBUG', 'on');
    define("CENTER_HOST","127.0.0.1");
}
define('ENV_NAME', $env);
const PORT = 8902;

function getRunPath()
{
    $path = Phar::running(false);
    if (empty($path)) return __DIR__;
    else return dirname($path)."/../agent_log";
}

