<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-2
 * Time: 下午9:49
 */
class LoadFile
{
    private $valid = false;
    public $configArray = array();

    public function __construct($config = array()){
        if(empty($this->configArray)){
            $this->configArray = $this->parse_config(include(ROOT_PATH."config/crontab.php"));
        }
    }

    public function current()
    {
        return current($this->configArray);
    }

    public function next()
    {
        $this->valid = (FALSE !== next($this->configArray));
    }

    public function key()
    {
        return key($this->configArray);
    }

    public function valid()
    {
        return $this->valid;
    }

    public function rewind()
    {
        $this->valid = (FALSE !== reset($this->configArray));
    }

    private function parse_config($config)
    {
        if(empty($config)){
            return array();
        }
        $conf = array();
        foreach ($config as $key => $val) {
            $conf[$key] = array(
                "name" => $val["name"],
                "time" => $val["time"],
                "unique" => $val["unique"],
                "task" => $val["task"]
            );
        }
        return $conf;
    }
}