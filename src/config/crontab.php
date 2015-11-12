<?php
return array(
    'taskid1' =>
        array(
            'name' => 'php -i',  //任务名称
            'time' => '* * * * * *',//定时规则
            "unique" => 1, //排他数量，如果已经有这么多任务在执行，即使到了下一次执行时间，也不执行
            'parse'  => 'Cmd',//命令处理类
            'task' =>
                array(
                    'cmd'    => 'php -i',//命令
                    'ext' => '',//附加属性
                ),
        ),
    'taskid2' =>
        array(
            'name' => 'test',  //任务名称
            'time' => array("22:30","22:22:58","22:24:36"),
            "unique" => 1, //排他数量，如果已经有这么多任务在执行，即使到了下一次执行时间，也不执行
            "parse" =>"Gather",
            'task' =>
                array(
                    'cmd'    => 'gather',//命令
                    'ext' => '',//附加属性
                ),
        ),
);
