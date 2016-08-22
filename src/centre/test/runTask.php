<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午6:35
 */
include __DIR__."/../_init.php";

$server = new Lib\Service();
$server->addServers(["127.0.0.1:8902"]);
$ret = $server->call("Exec::run",["id"=>1111,"execute"=>"/bin/echo hello liuzhiming"])->getResult();