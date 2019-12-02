<?php
/**
 * soa服务的客户端工具类
 * Class Service
 * @package Lib
 */
namespace App;
use Swoole;
class Service extends Swoole\Client\SOA
{
    protected $namespace = "App";
    private static $insance = [];

    public function __construct($ip='',$port='')
    {
        parent::__construct();
        if (empty($ip) || empty($port)){
            $service = Swoole::$php->config["service"];
            $ip = $service["CENTRE_HOST"];
            $port = $service["CENTRE_PORT"];
        }
        $this->addServers(array($ip.':'.$port));
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
