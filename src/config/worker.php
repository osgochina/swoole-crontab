<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-3
 * Time: 下午10:10
 */


return array(
    "ReadBook"=>array(
        "name"=>"队列1",
        "processNum"=>1,
        "redis"=>array(
            "host"=>"127.0.0.1",
            "port"=>6379,
            "timeout"=>30,
            "db"=>0,
            "queue"=>"abc"
        )
    )
);