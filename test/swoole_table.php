<?php
/**
 * Created by PhpStorm.
 * User: ClownFish
 * Date: 15-10-11
 * Time: 下午9:16
 */

$table = new swoole_table(1024);
$table->column("id",swoole_table::TYPE_INT,4);
$table->column("name",swoole_table::TYPE_STRING,64);
$table->column('num', swoole_table::TYPE_FLOAT);
$table->create();
$table->set('tianfenghan@qq.com', array('id' => 145, 'name' => 'rango', 'num' => 3.1415));


$process = new swoole_process('callback_function', false,false);
$process->table = $table;
$pid = $process->start();
echo "aa\n";

function callback_function (swoole_process $worker)
{
    echo "bb\n";
    print_r($worker->table->get("tianfenghan@qq.com"));

}

swoole_process::wait();