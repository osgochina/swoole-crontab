<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 14-12-27
 * Time: 上午12:21
 */

return [
    [
        "name"=>"php -i",
        "time"=>'* * * * *',
        "task"=>[
            "type"=>"cli",
            "cmd" =>"php -i",
            "output"=>"/tmp/test.log"
        ]
    ],
    [
        "name"=>"gearman",
        "time"=>'* * * * *',
        "task"=>[
            "type"=>"gearman",
            "services"=>"127.0.0.1:4730",
            "function"=>"tool/sendMail"
        ],
    ]
];