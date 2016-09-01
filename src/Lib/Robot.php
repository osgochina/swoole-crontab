<?php
/**
 * 中心服中的任务分发
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
    static private $ips;
    

    static private $column = [
        "ip" => [\swoole_table::TYPE_STRING, 15],
        "port" => [\swoole_table::TYPE_INT, 4],
        "lasttime"=>[\swoole_table::TYPE_INT, 8],
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
        if (self::$table->set($ip.$port,["ip"=>$ip,"port"=>$port,"lasttime"=>time()])){
            return true;
        }
        return false;
    }

    /**
     * 清除过期的worker
     */
    public static function clean()
    {
        if (count(self::$table)>0){
            $keys = [];
            foreach (self::$table as $k=>$v){
                if ($v["lasttime"] < time()-10){
                    $keys[] = $k;
                }
            }

            foreach ($keys as $k){
                self::$table->del($k);
            }
        }
    }


    private static function loadIps()
    {
        foreach (self::$table as $k=>$v){
            self::$ips[$k] = $v;
        }
    }

    /**
     * 执行任务
     * @param $task
     * @return bool|null
     */
    public static function Run($task)
    {
        self::loadIps();//载入配置到本地变量

        if (($robot = self::selectWorker()) == false){
            return false;
        }
        if (!self::sendTask($robot,$task)){
            TermLog::log("task业务运行失败:".json_encode($task),$task["id"]);
            return false;
        }
        return true;
    }

    /**
     * 分发任务
     * @param $robot
     * @param $task
     * @return bool
     */
    private static function sendTask($robot,$task)
    {
        TermLog::log("task发送给:".$robot["ip"].":".$robot["port"],$task["id"]);
        $server = new Service($robot["ip"],$robot["port"]);
        $rect = $server->call("Exec::run",$task);
        $rect->getResult();
        if($rect->code == Swoole\Client\SOA_Result::ERR_CLOSED || $rect->code == Swoole\Client\SOA_Result::ERR_CONNECT){
            TermLog::log($robot["ip"].":".$robot["port"]."已停止服务,code:".$rect->code,$task["id"]);
            unset(self::$ips[$robot["ip"].$robot["port"]]);
            unset($server);
            if (($robot = self::selectWorker()) == false){
                return false;
            }
            return self::sendTask($robot,$task);
        }
        unset($server);
        return true;
    }

    /**
     * 选择能执行任务的worker
     * @return bool
     */
    private static function selectWorker()
    {
        $num = count(self::$ips);
        if (!$num){
            Flog::log("No workers available");
            return false;
        }
        $rand = rand(1,$num);
        $n=0;
        foreach (self::$ips as $k=>$robot)
        {
            $n++;
            if ($rand == $num){
                return $robot;
            }
        }
        return false;
    }


}