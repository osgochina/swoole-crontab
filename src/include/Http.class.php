<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 15-1-8
 * Time: 下午7:55
 */

class Http
{
    static public $route = array(
        array("/conf/","getcrontab",'get',true),
        array("/log/","loglist",'get',false),
    );
    static public $host = "127.0.0.1";
    static public $port = 9501;
    static public $name = "lzm_Http";

    static public $http;
    static public $manager;
    static public $worker;

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
                    if($rte[3]){
                        $data = array(
                            "get"=>isset($request->get)?$request->get:"",
                            "post"=>isset($request->post)?$request->post:""
                        );
                        self::$worker->write($rte[1]."#@#".json_encode($data));
                        $return = self::$worker->read();
                        $response->end($return);
                        return true;
                    }else{
                        return call_user_func_array(array(new Manager(),$rte[1]."_http"),array("request"=>$request,"response"=>$response));
                    }
                }
            }
        }
        return false;
    }

    public function run($worker)
    {
        self::$worker = $worker;
        $worker->name(self::$name);
        self::http_server();
        self::start();
    }

}
