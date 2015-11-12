<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-3
 * Time: 下午10:10
 */


return array(
    //key是要加载的worker类名
    "ReadBook"=>array(
        "name"=>"队列1",            //备注名
        "processNum"=>1,           //启动的进程数量
        "redis"=>array(
            "host"=>"127.0.0.1",    // redis ip
            "port"=>6379,           // redis端口
            "timeout"=>30,          // 链接超时时间
            "db"=>0,                // redis的db号
            "queue"=>"abc"          // redis队列名
        )
    )
);