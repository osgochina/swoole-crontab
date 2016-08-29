<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午3:42
 */

include __DIR__."/../_init.php";

$server = new Lib\Service();
$server->addServers(["127.0.0.1:8901"]);
$task = [
    "taskname"=>"测试任务1",
    "rule"=>"* * * * * *",
    "unique"=>"0",
    "execute"=>"echo 'aa' ",
];
$ret = $server->call("Tasks::add",$task)->getResult();
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