<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-2
 * Time: 下午10:15
 */

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS."../src/");

spl_autoload_register(function ($name) {
    $file_path = ROOT_PATH . "include" . DS . $name . ".class.php";
    if(!file_exists($file_path)){
        $file_path = ROOT_PATH . "include" . DS ."LoadConfig".DS. $name . ".class.php";
    }
    include $file_path;
});

foreach((new Tasks()) as $key=>$val){
    print_r($key);
    print_r($val);
}