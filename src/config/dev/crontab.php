<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 14-12-27
 * Time: 上午12:21
 */

return [
    [
        "id"=>"taskid1",
        "name"=>"php -i",
        "time"=>'*/4 * * * * *',
        "task"=>[
            "parse"=>"Cmd",
            "cmd" =>"php -i",
            "output"=>"/tmp/test.log"
        ]
    ],
    [
        "id"=>"taskid2",
        "name"=>"gearman",
        "time"=>'*/8 * * * * *',
        "task"=>[
            "parse"=>"Gearman",
            "services"=>"127.0.0.1:4730",
            "function"=>"tool/sendMail"
        ],
    ]
];