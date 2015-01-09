<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 15-1-8
 * Time: 下午7:55
 */
require "Manager.class.php";
class Http
{
    static public $route = array(
        array("/abc/","getcrontab",'get'),
    );
    static public $host = "127.0.0.1";
    static public $port = 8080;

    static public $http;
    static public $manager;

    static public function http_server()
    {
        self::$http = new swoole_http_server(self::$host,self::$port,SWOOLE_BASE);
    }

    static public function start()
    {
        self::$manager = new Manager();
        self::$http->on('request',function($request,$response){
            if(!self::route($request,$response)){
                $response->status(404);
                $response->end("404 not found");
            }
        });
        self::$http->start();
    }

    static public function route($request,$response)
    {
        $method = $request->server["REQUEST_METHOD"];
        $path = $request->server["PATH_INFO"];
        foreach(self::$route as $rte){
            $pattern = str_replace("/",'\/',$rte[0]);
            preg_match("/$pattern/",$path,$matches);

            if(!empty($matches)){
                if(strtolower($rte[2]) == strtolower($method)){
                    call_user_func_array(array(self::$manager,$rte[1]),array("request"=>$request,"response"=>$response));
                    return true;
                }
            }
        }
        return false;
    }

}
Http::http_server();
Http::start();
