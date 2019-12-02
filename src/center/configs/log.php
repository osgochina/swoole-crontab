<?php
$log['master'] = array(
    'type' => 'FileLog',
    'cut_file' => true,
    'file' => getRunPath() . '/logs/application.log',
);
$log['term'] = array(
    'type' => 'FileLog',
    'cut_file' => true,
    'file' => getRunPath() . '/logs/term.log',
);
return $log;