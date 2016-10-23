<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-10-20
 * Time: 下午2:18
 */
require_once __DIR__ . '/_init.php';
$client = new Lib\Client(CENTER_HOST,CENTRE_PORT);
$ret = $client->call("Termlog::cleanLogs")->getResult(30);
var_dump($ret);