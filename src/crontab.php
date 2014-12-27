<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 上午11:59
 */


date_default_timezone_set('Asia/Shanghai');
define('APP_DEBUG', true);
define('APP_ENVIRONMENT', 'Dev');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS);
spl_autoload_register(function($name){
    $file_path= ROOT_PATH."include".DS.$name.".class.php";
    include $file_path;
});

TurnTable::init();

