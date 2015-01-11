<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-1-11
 * Time: 下午8:40
 */

print_r($argv);
$pipe = $argv[1];
$fp = fopen("php://fd/".$pipe,"a");
$http = new swoole_http_server("127.0.0.1",9501,SWOOLE_BASE);

$http->on('request',function($request,$response) use($fp){

    fwrite($fp,rand(100,100000)."->ok");
    $response->end(fread($fp,4096));
});
$http->start();