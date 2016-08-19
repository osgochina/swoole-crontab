<?php
define('SERVICE', true);
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);

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
Swoole::$php->config->setPath(__DIR__ . '/configs/' . ENV_NAME);//共有配置


