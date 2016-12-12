<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/data/www/public/");

define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);

function getRunPath()
{
    $path = Phar::running(false);
    if (empty($path)) return __DIR__;
    else return dirname($path)."/../crontab_log";
}

const LOAD_SIZE = 8192;//最多载入任务数量
const TASKS_SIZE = 1024;//同时运行任务最大数量
const ROBOT_MAX = 128;//同时挂载worker数量
const WORKER_NUM = 4;//worker进程数量
const TASK_NUM = 5;//task进程数量


define("CENTRE_PORT",8901);
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

require_once 'framework/libs/lib_config.php';
Swoole::$php->config->setPath(__DIR__ . '/configs/' . ENV_NAME);//共有配置
Swoole::$php->config->setPath(__DIR__ . '/configs');//共有配置
Swoole\Loader::addNameSpace('App', __DIR__ . '/App');
Swoole\Loader::addNameSpace('Lib', __DIR__ . '/Lib');


