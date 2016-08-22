<?php
namespace Lib;
use Swoole;
class Service extends Swoole\Client\SOA
{
    protected $namespace = "App";
    private static $insance;

    public static function getInstance($id = null)
    {
        if (!empty(self::$insance)){
            return self::$insance;
        }
        $config = Swoole::$php->config["crontab"];
        $host = $config["centre_host"];
        $port = $config["centre_port"];
        $insance = new self($id);
        $insance->addServers(array($host.':'.$port));
        self::$insance = $insance;
        return self::$insance;
        
    }

    function call()
    {
        $args = func_get_args();
        return $this->task($this->namespace . '\\' . $args[0], array_slice($args, 1));
    }
}
