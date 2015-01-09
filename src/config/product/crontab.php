<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 上午12:21
 */

return array(
    "taskid1"=>array(
        "name" => "php -i",
        "time" => '* * * * * *',
        "task" => array(
            "parse"  => "Cmd",
            "cmd"    => "php -i",
            "output" => "/tmp/test.log"
        )
    ),
    "taskid2"=>array(
        "name" => "gearman",
        "time" => '* * * * * *',
        "task" => array(
            "parse"    => "Gearman",
            "services" => "127.0.0.1:4730",
            "function" => "tool/sendMail"
        ),
    ),
    "taskid3"=>array(
        "name" => "gearman",
        "time" => '* * * * * *',
        "task" => array(
            "parse"    => "Gearman",
            "services" => "127.0.0.1:4730",
            "function" => "tool/sendMail"
        ),
    ),
);