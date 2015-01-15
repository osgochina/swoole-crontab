<?php
return array(
    'taskid1' =>
        array(
            'name' => 'php -i',  //任务名称
            'time' => '* * * * * *',//定时规则
            "unique"=>true,//是否排他，当一个任务在执行的时候，即时到了下一次执行时间，也不执行
            'task' =>
                array(
                    'parse'  => 'Cmd',//命令处理类
                    'cmd'    => 'php -i',//命令
                    'output' => '/tmp/test.log',
                ),
        ),
);