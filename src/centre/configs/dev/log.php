<?php
$log['master'] = array(
    'type' => 'FileLog',
    'cut_file' => true,
    'file' => WEBPATH . '/logs/application.log',
);
$log['term'] = array(
    'type' => 'FileLog',
    'cut_file' => true,
    'file' => WEBPATH . '/logs/term.log',
);
return $log;