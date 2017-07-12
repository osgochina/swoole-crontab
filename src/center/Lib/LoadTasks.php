<?php

/**
 * task任务的管理类
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午5:44
 */

namespace Lib;

use Swoole;

class LoadTasks
{
    static private $column = [
        "execNum" => [\swoole_table::TYPE_INT, 1],
        "runStatus" => [\swoole_table::TYPE_INT, 1],
        "runTimeStart" => [\swoole_table::TYPE_INT, 8],
        "runUpdateTime" => [\swoole_table::TYPE_INT, 8],
        "taskname" => [\swoole_table::TYPE_STRING, 256],
        "gid" => [\swoole_table::TYPE_INT, 4],
        "rule" => [\swoole_table::TYPE_STRING, 32],
        "runnumber" => [\swoole_table::TYPE_INT, 1],
        "status" => [\swoole_table::TYPE_INT, 1],
        "runuser" => [\swoole_table::TYPE_STRING, 64],
        "execute" => [\swoole_table::TYPE_STRING, 512],
        "agents" => [\swoole_table::TYPE_STRING, 512],
    ];


    const tablename = "crontab";

    static private $table;


    const T_START = 0;//正常
    const T_STOP = 1;//暂停

    const RunStatusError = -1;//不符合条件，不运行
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
        //创建config table
        self::createConfigTable();
        //载入tasks
        self::loadTasks();
    }

    /**
     * 创建配置表
     */
    private static function createConfigTable()
    {
        self::$table = new \swoole_table(LOAD_SIZE);
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
        $db = table("crontab");
        $start = 0;
        while (true) {
            $where["limit"] = $start . ",1000";
            $tasks = $db->gets($where);
            if (empty($tasks)) {
                break;
            }
            foreach ($tasks as $task) {
                if (count(self::$table) > LOAD_SIZE) {
                    return true;
                }
                self::$table->set($task["id"],
                    [
                        "taskname" => $task["taskname"],
                        "rule" => $task["rule"],
                        "gid" => $task["gid"],
                        "runnumber" => $task["runnumber"],
                        "status" => $task["status"],
                        "runuser" => $task["runuser"],
                        "execute" => $task["execute"],
                        "agents" => $task["agents"],
                    ]
                );
            }
            $start += 1000;
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
     * @return array|bool
     */
    public static function saveTasks($tasks)
    {
        $ids = [];
        $db = table("crontab");
        foreach ($tasks as $task) {
            $task["execute"] = self::merge_spaces($task["execute"]);
            $ids[] = $task["id"];
            if (self::$table->exist($task["id"])) {
                if (!$db->set($task["id"], $task)) {
                    return false;
                }
            } else {
                $task["createtime"] = date("Y-m-d H:i:s");
                if (!$db->put($task)) {
                    return false;
                }
            }
            self::$table->set($task["id"],
                [
                    "taskname" => $task["taskname"],
                    "rule" => $task["rule"],
                    "gid" => $task["gid"],
                    "runnumber" => $task["runnumber"],
                    "status" => $task["status"],
                    "runuser" => $task["runuser"],
                    "execute" => $task["execute"],
                    "agents" => $task["agents"],
                ]
            );
        }
        return $ids;
    }

    static public function merge_spaces($string)
    {
        return preg_replace("/\s(?=\s)/", "\\1", $string);
    }

    /**
     * 更新任务
     * @param $id
     * @param $task
     * @return bool
     */
    public static function updateTask($id, $task)
    {
        if (isset($task["execute"])) {
            $task["execute"] = self::merge_spaces($task["execute"]);
        }
        if (!table("crontab")->set($id, $task)) {
            return false;
        }
        if (!self::$table->set($id, $task)) {
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
        if (!table("crontab")->del($id)) {
            return false;
        }
        if (!self::$table->del($id)) {
            return false;
        }
        return true;
    }
}