<?php
$db['master'] = array(
    'type' => Swoole\Database::TYPE_MYSQLi,
    'host' => "192.168.1.15",
    'port' => 3306,
    'dbms' => 'mysql',
    'user' => "root",
    'passwd' => "root",
    'name' => "crontab",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => false, //MySQL长连接
);
return $db;