<?php
/**
 * soa服务的客户端工具类
 * Class Service
 * @package Lib
 */

namespace Lib;

use Lib\Robot;

class Client
{
    protected $namespace = "App";
    protected $fd = -1;
    protected static $insance = [];

    public function __construct($ip)
    {
        $ret = Robot::$table->get($ip);
        if (isset($ret["fd"]) && !empty($ret["fd"])) {
            $this->fd = $ret["fd"];
        }
    }

    public static function getInstance($ip = "")
    {
        if (isset(self::$insance[$ip]) && !empty(self::$insance[$ip])) {
            return self::$insance[$ip];
        }
        $insance = new self($ip);
        self::$insance[$ip] = $insance;
        return $insance;

    }

    function call()
    {
        $args = func_get_args();
        return $this->task($this->namespace . '\\' . $args[0], array_slice($args, 1));
    }

    protected function task($function, $params = array())
    {

        $data = SOAProtocol::encode($function, $params);
        $ret = CenterServer::$_server->send($this->fd, $data);
        return $ret;
    }
}
