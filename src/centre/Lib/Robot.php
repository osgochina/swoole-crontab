<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午3:27
 */

namespace Lib;

use Swoole;
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
            if (self::$table->set(1,["ip"=>$ip,"port"=>$port,"status"=>0])){
                return $client->close();
            }
        }
        return false;
    }

    /**
     * 运行任务
     * @param $task
     * @return bool|null
     */
    public static function Run($task)
    {
        $num = count(self::$table);
        if (!$num){
            return false;
        }
        $rand = rand(1,$num);
        $n=0;
        foreach (self::$table as $robot)
        {
            $n++;
            if ($rand == $num){
                $rect = Service::getInstance($robot["ip"],$robot["port"])->call("Exec::run",$task);
                $ret = $rect->getResult(30);
                if (empty($ret)){
                    if($rect->code == Swoole\Client\SOA_Result::ERR_CLOSED || $rect->code == Swoole\Client\SOA_Result::ERR_CONNECT){
                        //TODO 重新选择服务逻辑
                        Flog::log($robot["ip"].":".$robot["port"]."已停止服务");
                        return false;
                    }
                }
                return $ret;
            }
        }
        return true;
    }
}