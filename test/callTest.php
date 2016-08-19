<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午3:42
 */
if (!class_exists('Swoole', false))
{
    require_once '/data/www/public/framework/libs/Swoole/Loader.php';
    Swoole\Loader::addNameSpace('Swoole', '/data/www/public/framework/libs/Swoole');
    spl_autoload_register('\\Swoole\\Loader::autoload', true, true);
}

$service = new Swoole\Client\SOA();
$service->setServers(array('127.0.0.1:8808'));

$task = [
    "taskname"=>"测试任务1",
    "rule"=>"* * * * * *",
    "unique"=>"0",
    "execute"=>"php server.php start",
];
$ret = $service->task("App\\Tasks::add",[$task])->getResult();
var_dump($ret);

//$id = $ret["data"][0];
//$task = [];
//$task = [
//    $id,
//    ["status"=>"1"],
//];
//
//$ret = $service->task("App\\Tasks::update",$task)->getResult();
//var_dump($ret);
//
//$ret = $service->task("App\\Tasks::get",[$id])->getResult();
//var_dump($ret);
//
//$ret = $service->task("App\\Tasks::getList")->getResult();
//var_dump($ret);
//$ret = $service->task("App\\Tasks::delete",[$id])->getResult();
//var_dump($ret);
//
//$ret = $service->task("App\\Tasks::getList")->getResult();
//var_dump($ret);