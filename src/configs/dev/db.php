<?php
$db['master'] = array(
    'type' => Swoole\Database::TYPE_MYSQLi,
    'host' => "10.10.2.220",
    'port' => 3306,
    'dbms' => 'mysql',
    'user' => "root",
    'passwd' => "root",
    'name' => "swoole_crontab",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => false, //MySQL长连接
);
return $db;