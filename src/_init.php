<?php
define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);

define("LOAD_SIZE",8192);//最多载入任务数量
define("TASKS_SIZE",1024);//同时运行任务最大数量
define("ROBOT_MAX",8);//同时挂载worker数量
define("ROBOT_MAX_PROCESS",128);//单个worker同时执行任务数量

define("CENTRE_HOST","127.0.0.1");
define("CENTRE_PORT",8901);

$env = get_cfg_var('env.name');
if (empty($env))
{
    $env = 'product';
    define('DEBUG', 'off');
}
else
{
    define('DEBUG', 'on');
}
define('ENV_NAME', $env);

if (is_dir('/data/www/public/framework'))
{
    require_once '/data/www/public/framework/libs/lib_config.php';
}
else
{
    require_once __DIR__ . '/framework/libs/lib_config.php';
}
if (is_dir('/data/www/wwwroot/crontab/configs/'.ENV_NAME)){
    $config_dir = '/data/www/wwwroot/swoole-crontab/configs/'.ENV_NAME;
}else{
    $config_dir = __DIR__ . '/configs/';
}

Swoole::$php->config->setPath($config_dir);//共有配置
Swoole\Loader::addNameSpace('App', __DIR__ . '/App');
Swoole\Loader::addNameSpace('Lib', __DIR__ . '/Lib');


