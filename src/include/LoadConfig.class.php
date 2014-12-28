<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午3:46
 */

class LoadConfig
{
    static public $config_file;
    static protected $config;

    static protected function load_config()
    {
        if(is_dir(self::$config_file)){
            self::$config = include(self::$config_file."crontab.php");
        }elseif(is_file(self::$config_file)){
            self::$config = include(self::$config_file);
        }
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