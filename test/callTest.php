<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午3:42
 */

include __DIR__."/../src/_init.php";

$server = new Lib\Service("127.0.0.1","8901");
$task = [
    "taskname"=>"测试任务1",
    "rule"=>"* * * * * *",
    "unique"=>"0",
    "execute"=>"echo 'aa' ",
];
$ret = $server->call("Tasks::add",$task)->getResult();
var_dump($ret);