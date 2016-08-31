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
            if (self::$table->set($ip.$port,["ip"=>$ip,"port"=>$port])){
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
        if (($robot = self::selectWorker()) == false){
            return false;
        }
        if (!self::sendTask($robot,$task)){
            Flog::log("业务运行失败,task:".json_encode($task));
            return false;
        }
        return true;
    }

    private static function sendTask($robot,$task)
    {
        $rect = Service::getInstance($robot["ip"],$robot["port"])->call("Exec::run",$task);
        $rect->getResult();
        if($rect->code == Swoole\Client\SOA_Result::ERR_CLOSED || $rect->code == Swoole\Client\SOA_Result::ERR_CONNECT){
            Flog::log($robot["ip"].":".$robot["port"]."已停止服务");
            self::$table->del($robot["ip"].$robot["port"]);
            if (($robot = self::selectWorker()) == false){
                return false;
            }
            return self::sendTask($robot,$task);
        }
        return true;
    }

    private static function selectWorker()
    {
        $num = count(self::$table);
        if (!$num){
            Flog::log("No workers available");
            return false;
        }
        $rand = rand(1,$num);
        $n=0;
        foreach (self::$table as $k=>$robot)
        {
            $n++;
            if ($rand == $num){
                return $robot;
            }
        }
        return false;
    }


}