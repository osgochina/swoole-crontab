<?php
/**
 * 管理需要处理的任务
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午4:33
 */

namespace Lib;

class Tasks
{
    static public $table;

    static private $column = [
        "minute" => [\swoole_table::TYPE_STRING, 12],
        "sec" => [\swoole_table::TYPE_STRING, 12],
        "id" => [\swoole_table::TYPE_STRING, 20],
        "runid" => [\swoole_table::TYPE_STRING, 20],
        "runStatus" => [\swoole_table::TYPE_STRING, 2],
    ];

    /**
     * 创建配置表
     */
    public static function init()
    {
        self::$table = new \swoole_table(TASKS_SIZE*2);
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
        //清理完成任务
        self::clean();
        $tasks = LoadTasks::getTasks();
        if (count($tasks) > 0) {
            $time = time();
            foreach ($tasks as $id => $task) {
                if ($task["status"] != LoadTasks::T_START) {
                    continue;
                }
                $ret = ParseCrontab::parse($task["rule"], $time);
                if ($ret === false) {
                    Flog::log(ParseCrontab::$error);
                } elseif (!empty($ret)) {
                    $min = date("YmdHi");
                    $time = strtotime(date("Y-m-d H:i"));
                    foreach ($ret as $sec) {
                        if (count(self::$table) > TASKS_SIZE){
                            Flog::log("checkTasks fail ,because tasks size Max");
                            break;
                        }
                        $k = Donkeyid::getInstance()->dk_get_next_id();
                        self::$table->set($k, [
                            "minute" => $min,
                            "sec" => $time + $sec,
                            "id" => $id,
                            "runStatus" => LoadTasks::RunStatusNormal
                        ]);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 清理已执行过的任务
     */
    private static function clean()
    {
        $ids = [];
        $ids2 = [];
        $loadtasks = LoadTasks::getTasks();
        if (count(self::$table) > 0) {
            $minute = date("YmdHi");
            foreach (self::$table as $id => $task) {
                if ($task["runStatus"] == LoadTasks::RunStatusSuccess || $task["runStatus"] == LoadTasks::RunStatusFailed) {
                    $ids[] = $id;
                    continue;
                } else {
                    $info = $loadtasks->get($id);
                    if (!array_key_exists("timeout",$info) || $info["timeout"] <= 0){
                        continue;
                    }
                    $timeout = intval($info["timeout"]/60);
                    $timeout = $timeout > 1?$timeout:1;
                    if (intval($minute) > intval($task["minute"]) + $timeout) {
                        $ids[] = $id;
                        if ($task["runStatus"] == LoadTasks::RunStatusStart
                            || $task["runStatus"] == LoadTasks::RunStatusToTaskSuccess
                            || $task["runStatus"] == LoadTasks::RunStatusError
                        ) {
                            $ids2[] = $task["id"];
                        }
                    }
                }

            }
        }
        //删除
        foreach ($ids as $id) {
            self::$table->del($id);
        }
        //超时则把运行中的数量-1
        foreach ($ids2 as $tid) {
            $loadtasks->decr($tid, "execNum");
        }
    }

    /**
     * 获取当前可以执行的任务
     * @return array
     */
    public static function getTasks()
    {
        $data = [];
        if (count(self::$table) <= 0) {
            return [];
        }
        $min = date("YmdHi");

        foreach (self::$table as $k => $task) {
            if ($min == $task["minute"]) {
                if (time() == $task["sec"] && $task["runStatus"] == LoadTasks::RunStatusNormal) {
                    $data[$k] = $task["id"];
                }
            }
        }
        if (!empty($data)) {
            foreach ($data as $k => $val) {
                self::$table->set($k, ["runStatus" => LoadTasks::RunStatusStart, "runTimeStart" => time()]);
            }
        }
        return $data;
    }
}