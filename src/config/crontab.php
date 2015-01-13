<?php
return array(
    'taskid2' =>
        array(
            'name' => 'php -i',
            'time' => '* * * * * *',
            'task' =>
                array(
                    'parse'  => 'Cmd',
                    'cmd'    => 'php -i',
                    'output' => '/tmp/test.log',
                ),
        ),
);