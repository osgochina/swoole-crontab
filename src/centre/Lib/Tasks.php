<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午4:33
 */

namespace Lib;

use Swoole;
class Tasks
{
    static private $table;

    static private $column = [
        "minute" => [\swoole_table::TYPE_INT, 8],
        "sec" => [\swoole_table::TYPE_INT, 8],
        "id" => [\swoole_table::TYPE_INT, 8],
    ];
    /**
     * 创建配置表
     */
    public static function init()
    {
        $conf = Swoole::$php->config["crontab"];
        $tasks_size = (isset($conf["tasks_size"]) && $conf["tasks_size"] > 0) ? $conf["tasks_size"] : 1024;
        self::$table = new \swoole_table($tasks_size);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();
    }

    /**
     * 每分钟执行一次，判断下一分钟需要执行的任务
     */
    public static function checkTasks()
    {
        $tasks = LoadTasks::getTasks();
        if (count($tasks) > 0){
            $time = time();
            foreach ($tasks as $id=>$task){
                $ret = ParseCrontab::parse($task["rule"], $time);
                if ($ret === false) {
                    Flog::log(ParseCrontab::$error);
                } elseif (!empty($ret)) {
                    $min = date("YmdHi");
                    $time = strtotime(date("Y-m-d H:i"));
                    foreach ($ret as $sec){
                        $k =Donkeyid::getInstance()->dk_get_next_id();
                        self::$table->set($k,["minute"=>$min,"sec"=>$time+$sec,"id"=>$id]);
                    }
                }
            }
        }
        self::clean();
    }

    /**
     * 清理已执行过的任务
     */
    private static function clean()
    {
        $ids = [];
        if (count(self::$table) > 0){
            $minute = date("YmdHi");
            foreach (self::$table as $id=>$task){
                if (intval($minute) > intval($task["minute"])){
                    $ids[] = $id;
                }
            }
        }
        //删除
        foreach ($ids as $id){
            self::$table->del($id);
        }
    }

    /**
     * 获取当前可以执行的任务
     * @return array
     */
    public static function getTasks()
    {
        $data = [];
        if (count(self::$table) <= 0){
            return [];
        }
        $min = date("YmdHi");
        $time = time();
        foreach (self::$table as $task){
            if ($min == $task["minute"] ){
                if ($time == $task["sec"]){
                    $data[] = $task["id"];
                }
            }
        }
        return $data;
    }
}