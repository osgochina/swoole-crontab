<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午3:27
 */

namespace Lib;


class Robot
{

    static private $table;

    static private $column = [
        "ip" => [\swoole_table::TYPE_STRING, 15],
        "port" => [\swoole_table::TYPE_INT, 4],
        "status" => [\swoole_table::TYPE_INT, 1],
    ];
    public static function init()
    {
        $conf = \Swoole::$php->config["crontab"];
        $robot_num_max = (isset($conf["robot_num_max"]) && $conf["robot_num_max"] > 0) ? $conf["robot_num_max"] : 2;
        self::$table = new \swoole_table($robot_num_max);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();
    }

    /**
     * 注册服务
     * @param $ip
     * @param $port
     * @return bool
     */
    public static function register($ip,$port)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        if($client->connect($ip,$port)){
            if (self::$table->set($ip.":".$port,["ip"=>$ip,"port"=>$port,"status"=>0])){
                return $client->close();
            }
        }
        return false;
    }
}