<?php 
 return array (
  'taskid1' => 
  array (
    'name' => 'php -i',
    'time' => '1 * * 8 * *',
    'task' => 
    array (
      'parse' => 'Cmd',
      'cmd' => 'php -i',
      'output' => '/tmp/test.log',
    ),
  ),
  'taskid2' =>
      array (
          'name' => 'php -i',
          'time' => '* 42-43 * * * *',
          'task' =>
              array (
                  'parse' => 'Cmd',
                  'cmd' => 'php -i',
                  'output' => '/tmp/test.log',
              ),
      ),
);