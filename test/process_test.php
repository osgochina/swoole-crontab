<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-1-11
 * Time: 下午8:42
 */

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS);
function fork($worker)
{
    $binpath = $_SERVER["_"];
    $worker->exec($binpath,array(ROOT_PATH."http_test.php",$worker->pipe));
}
$process = new swoole_process("fork");
$process->start();

function process_run($worker)
{
    echo "abc\n";
    $worker->exit(0);
    exit;
}
function create_process()
{
    $process = new swoole_process("process_run");
    $process->start();
}

swoole_timer_add(1000, function ($interval) {
   create_process();
   create_process();
});

swoole_process::signal(SIGCHLD, function ($signo) {
    while( $pid = pcntl_wait($status,WNOHANG)){
        echo $pid."\n";
    };
});

swoole_event_add($process->pipe, function ($pipe) use ($process) {
    $ret = $process->read();
    echo $ret;
    $process->write($ret);
});