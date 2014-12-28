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

    /**
     * 返回格式化好的任务配置
     * @return array
     */
    static public function get_config()
    {
        self::load_config();
        return self::parse_config();
    }

    /**
     * 从配置文件载入配置
     */
    static protected function load_config()
    {
        if (is_dir(self::$config_file)) {
            self::$config = include(self::$config_file . "crontab.php");
        } elseif (is_file(self::$config_file)) {
            self::$config = include(self::$config_file);
        }
    }

    /**
     * 格式化配置文件中的配置
     * @return array
     */
    static protected function parse_config()
    {
        $config = array();
        foreach (self::$config as $val) {
            $config[$val["id"]] = array(
                "time" => $val["time"],
                "task" => $val["task"]
            );
        }
        return $config;
    }
}