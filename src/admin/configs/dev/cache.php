<?php
$cache['session'] = array(
    'type' => 'FileCache',
    'cache_dir' => WEBPATH . '/runtime/cache/filecache/',
);
$cache['master'] = array(
    'type' => 'FileCache',
    'cache_dir' => WEBPATH . '/runtime/cache/filecache/',
);
return $cache;