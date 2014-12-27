<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 14-12-27
 * Time: 下午3:46
 */

class LoadConfig
{
    static public $config_path;
    static protected $config;

    static protected function load_config()
    {
        self::$config = include(self::$config_path."crontab.php");
    }

    static protected function parse_config()
    {
        $config = array();
        foreach(self::$config as $val){
            $config[$val["id"]] = array(
                "time"=>$val["time"],
                "task"=>$val["task"]
            );
        }
        return $config;
    }

    static public function get_config()
    {
        self::load_config();
        return self::parse_config();
    }
}