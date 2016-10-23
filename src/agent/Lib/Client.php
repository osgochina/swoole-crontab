<?php
/**
 * soa服务的客户端工具类
 * Class Service
 * @package Lib
 */
namespace Lib;

class Client extends SOAClient
{
    protected $namespace = "App";
    private static $insance = [];

    public function __construct()
    {
        parent::__construct();
        $this->addServers(array(CENTER_HOST.':'.CENTER_PORT));
    }

    public static function getInstance($ip="",$port="")
    {
        if (isset(self::$insance[$ip.":".$port]) && !empty(self::$insance[$ip.":".$port])){
            return self::$insance[$ip.":".$port];
        }
        $insance = new self($ip,$port);
        self::$insance[$ip.":".$port] = $insance;
        return $insance;
        
    }

    function call()
    {
        $args = func_get_args();
        return $this->task($this->namespace . '\\' . $args[0], array_slice($args, 1));
    }
}
