<?php
namespace Lib;
use Swoole;
class Service extends Swoole\Client\SOA
{
    protected $namespace = "App";
    private static $insance = [];

    public static function getInstance($ip="",$port="")
    {
        if (empty($ip) || empty($port)){
            $config = Swoole::$php->config["crontab"];
            $ip = $config["centre_host"];
            $port = $config["centre_port"];
        }
        if (isset(self::$insance[$ip.":".$port]) && !empty(self::$insance[$ip.":".$port])){
            return self::$insance[$ip.":".$port];
        }
        $insance = new self(null);
        $insance->addServers(array($ip.':'.$port));
        self::$insance[$ip.":".$port] = $insance;
        return $insance;
        
    }

    function call()
    {
        $args = func_get_args();
        return $this->task($this->namespace . '\\' . $args[0], array_slice($args, 1));
    }
}
