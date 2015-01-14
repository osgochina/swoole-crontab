<?php
return array(
    'taskid2' =>
        array(
            'name' => 'php test',
            'time' => '* * * * * *',
            "unique"=>true,
            'task' =>
                array(
                    'parse'  => 'Cmd',
                    'cmd'    => 'php /var/www/squire/src/test.php',
                    'output' => '/tmp/test.log',
                ),
        ),
);