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

    static public $table;
    static public $groupTable;
    static public $aTable;
    static private $ips;

    static private $column = [
        "lasttime"=>[\swoole_table::TYPE_INT, 8],
    ];
    static private $aColumn = [
        "ip" => [\swoole_table::TYPE_STRING, 15],
        "port" => [\swoole_table::TYPE_INT, 4],
    ];
    public static function init()
    {
        self::$table = new \swoole_table(ROBOT_MAX);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();

        self::$aTable = new \swoole_table(1024);
        foreach (self::$aColumn as $key => $v) {
            self::$aTable->column($key, $v[0], $v[1]);
        }
        self::$aTable->create();
        self::loadAgents();
    }

    /**
     * 载入分组代理信息
     * @return bool
     */
    public static function loadAgents()
    {
        $agents = table("agents")->gets(["status"=>0]);
        if (empty($agents))
        {
            return false;
        }
        foreach ($agents as $agent){
            self::$aTable->set($agent["id"],[
                "ip"=>$agent["ip"],
                "port"=>$agent["port"],
            ]);
        }
        return true;
    }

    /**
     * 注册服务
     * @param $ip
     * @param $port
     * @return bool
     */
    public static function register($ip,$port)
    {
        if (self::$table->set($ip.":".$port,["lasttime"=>time()])){
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

        if (($robot = self::selectWorker($task["agents"])) == false){
            return false;
        }
        if (!self::sendTask($robot,$task)){
            TermLog::log($task["runid"],$task["id"],"发送业务失败",$task);
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
        TermLog::log($task["runid"],$task["id"],"发送到agent服务器",$robot);
        $client = new Client(explode(":",$robot)[0],explode(":",$robot)[1]);
        $rect = $client->call("Exec::run",$task);
        $rect->getResult(3);
        if($rect->code == Swoole\Client\SOA_Result::ERR_CLOSED || $rect->code == Swoole\Client\SOA_Result::ERR_CONNECT){
            TermLog::log($task["runid"],$task["id"],"agent服务器停止服务",$robot."已停止服务,code:".$rect->code);
            unset(self::$ips[$robot]);
            unset($server);
            if (($robot = self::selectWorker($task["agents"])) == false){
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
    private static function selectWorker($agents)
    {
        $num = count(self::$ips);
        if (!$num){
            Flog::log("No workers available");
            return false;
        }
        $agents = explode(",",$agents);
        if (empty($agents))
        {
            Flog::log("没有配置运行服务器");
            return false;
        }

        $rand = rand(1,count($agents));
        $n=0;
        foreach ($agents as $k=>$aid)
        {
            $n++;
            if ($rand <= $n){
                $aip = self::$aTable->get($aid);
                if (empty($aip)) continue;
                $robot = $aip["ip"].":".$aip["port"];
                if (!isset(self::$ips[$robot])) continue;
                return $robot;
            }
        }
        Flog::log("没有选中任何服务器,服务器数量:".$num.",随机数:".$rand);
        return false;
    }


}