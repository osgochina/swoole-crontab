<?php
namespace Lib;
use Swoole;
class Service extends Swoole\Client\SOA
{
    protected $namespace = "App";
    private static $insance = [];

    public function __construct($ip='',$port='')
    {
        parent::__construct();
        if (empty($ip) || empty($port)){
            $config = Swoole::$php->config["crontab"];
            $ip = $config["centre_host"];
            $port = $config["centre_port"];
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
