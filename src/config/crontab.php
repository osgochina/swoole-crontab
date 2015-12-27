<?php
return array(
    'taskid1' =>
        array(
            'taskname' => 'php -i',  //任务名称
            'rule' => '* * * * * *',//定时规则
            "unique" => 1, //排他数量，如果已经有这么多任务在执行，即使到了下一次执行时间，也不执行
            'execute'  => 'Cmd',//命令处理类
            'args' =>
                array(
                    'cmd'    => 'php -i',//命令
                    'ext' => '',//附加属性
                ),
        ),
    'taskid2' =>
        array(
            'taskname' => 'test',  //任务名称
            'rule' => array("22:30","22:22:58","22:24:36"),
            "unique" => 1, //排他数量，如果已经有这么多任务在执行，即使到了下一次执行时间，也不执行
            "execute" =>"Gather",
            'args' =>
                array(
                    'cmd'    => 'gather',//命令
                    'ext' => '',//附加属性
                ),
        ),
);
