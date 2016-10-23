<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-6-12
 * Time: 下午4:41
 */
define('APPSPATH', dirname(__DIR__) . '/admin');
define('WEBPATH', dirname(__DIR__));


$env = get_cfg_var('env.name');
if (empty($env))
{
    $env = 'product';
    define('DEBUG', 'off');
    define('WEBROOT', 'http://crontab.oa.com');
}elseif ($env == "dev"){
    define('WEBROOT', 'http://crontab.mysite.com');
}
else
{
    define('WEBROOT', 'http://test.crontab.oa.com');

    define('DEBUG', 'on');
}

define('ENV_NAME', $env);
define('PUBLIC_PATH', '/data/www/public/');
require_once PUBLIC_PATH.'framework/libs/lib_config.php';

Swoole::$php->config->setPath(APPSPATH . '/configs');
Swoole::$php->config->setPath(APPSPATH . '/configs/' . ENV_NAME);
$php->runMVC();