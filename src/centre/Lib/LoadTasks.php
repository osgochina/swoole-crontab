<?php

/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午5:44
 */
namespace Lib;

use Swoole;

class LoadTasks
{
    private static $createTable = "
    CREATE TABLE `crontab` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `taskname` varchar(32) NOT NULL,
  `rule` varchar(32) NOT NULL COMMENT '规则 可以是crontab规则也可以是启动的间隔时间',
  `unique` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0 唯一任务 大于0表示同时可并行的任务进程个数',
  `execute` varchar(512) NOT NULL COMMENT '运行命令行',
  `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0 正常  1 暂停',
  `createtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    ";

    static private $column = [
        "runStatus" => [\swoole_table::TYPE_INT, 1],
        "runTimeStart" => [\swoole_table::TYPE_INT, 8],
        "runUpdateTime" => [\swoole_table::TYPE_INT, 8],
        "taskname" => [\swoole_table::TYPE_STRING, 32],
        "rule" => [\swoole_table::TYPE_STRING, 32],
        "unique" => [\swoole_table::TYPE_INT, 1],
        "status" => [\swoole_table::TYPE_INT, 1],
        "execute" => [\swoole_table::TYPE_STRING, 512],
    ];


    const tablename = "crontab";
    static private $table;
    static private $db;


    const T_START = 0;//正常
    const T_STOP = 1;//暂停

    const RunStatusNormal = 0;//未运行
    const RunStatusStart = 1;//准备运行
    const RunStatusToTaskSuccess = 2;//发送任务成功
    const RunStatusToTaskFailed = 3;//发送任务失败
    const RunStatusSuccess = 4;//运行成功
    const RunStatusFailed = 5;//运行失败

    /**
     * 初始化任务表
     */
    public static function init()
    {
        $db = new Swoole\Model(Swoole::getInstance());
        $dbname = Swoole::$php->config["db"]["master"]["name"];
        $data = $db->db->query("SELECT count(*) as total FROM information_schema.TABLES WHERE table_name = '" . self::tablename . "' AND TABLE_SCHEMA = '{$dbname}'")->fetch();
        if (!empty($data) && intval($data["total"]) == 0) {
            $stmt = $db->db->query(self::$createTable);
            if ($stmt) {
                Flog::log("执行sql:" . self::$createTable . "执行成功");
            } else {
                Flog::log("执行sql:" . self::$createTable . "执行失败");
            }
            Flog::flush();
        }
        //创建config table
        self::createConfigTable();
        //载入tasks
        self::$db = table(self::tablename);
        self::loadTasks();
    }

    /**
     * 创建配置表
     */
    private static function createConfigTable()
    {
        $conf = Swoole::$php->config["crontab"];
        $load_size = (isset($conf["load_size"]) && $conf["load_size"] > 0) ? $conf["load_size"] : 1024;
        self::$table = new \swoole_table($load_size);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();
    }


    /**
     * 载入任务
     * @return bool
     */
    private static function loadTasks()
    {
        $count = self::$db->count([]);
        if (empty($count)) {
            Flog::log("未加载到表" . self::tablename . "中的数据");
            return false;
        }
        $where["limit"] = "0," . $count;
        $tasks = self::$db->gets($where);
        foreach ($tasks as $task) {
            self::$table->set($task["id"],
                [
                    "taskname" => $task["taskname"],
                    "rule" => $task["rule"],
                    "unique" => $task["unique"],
                    "status" => $task["status"],
                    "execute" => $task["execute"],
                ]
            );
        }
        return true;
    }
    /**
     * 获取需要执行的任务
     * @return array
     */
    public static function getTasks()
    {
        return self::$table;
    }

    /**
     * 保存tasks
     * @param $tasks
     */
    public static function saveTasks($tasks)
    {
        $ids = [];
        foreach ($tasks as $task) {
            $ids[] = $task["id"];
            if (self::$table->exist($task["id"])){
                if (!self::$db->set($task["id"],$task)){
                    return false;
                }
            }else{
                $task["createtime"] = date("Y-m-d H:i:s");
                $task["status"] = 0;
                if (!self::$db->put($task)){
                    print_r($task);
                    return false;
                }
            }
            self::$table->set($task["id"],
                [
                    "taskname" => $task["taskname"],
                    "rule" => $task["rule"],
                    "unique" => $task["unique"],
                    "status" => $task["status"],
                    "execute" => $task["execute"],
                ]
            );
        }
        return $ids;
    }

    /**
     * 更新任务
     * @param $id
     * @param $task
     * @return bool
     */
    public static function updateTask($id,$task)
    {
        if (!self::$db->set($id,$task)){
            return false;
        }
        if (!self::$table->set($id,$task)){
            return false;
        }
        return true;
    }

    /**
     * 删除任务
     * @param $id
     * @return bool
     */
    public static function delTask($id)
    {
        if (!self::$db->del($id)){
            return false;
        }
        if (!self::$table->del($id)){
            return false;
        }
        return true;
    }
}