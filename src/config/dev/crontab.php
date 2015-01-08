<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 上午12:21
 */

return array(
    array(
        "id"   => "taskid1",
        "name" => "php -i",
        "time" => '* * * * * *',
        "task" => array(
            "parse"  => "Cmd",
            "cmd"    => "php -i",
            "output" => "/tmp/test.log"
        )
    ),
    array(
        "id"   => "taskid2",
        "name" => "gearman",
        "time" => '* * * * * *',
        "task" => array(
            "parse"    => "Gearman",
            "services" => "127.0.0.1:4730",
            "function" => "tool/sendMail"
        ),
    ),
    array(
        "id"   => "taskid3",
        "name" => "gearman",
        "time" => '* * * * * *',
        "task" => array(
            "parse"    => "Gearman",
            "services" => "127.0.0.1:4730",
            "function" => "tool/sendMail"
        ),
    ),
);