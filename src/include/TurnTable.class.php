<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午2:46
 */
class TurnTable
{
    static protected $turntable1 = array();  //任务转盘1
    static protected $turntable2 = array();  //任务转盘2
    static protected $cur_turntable = 1;     //当前运行的任务转盘
    static protected $current = 0;           //当前运行的秒数

    /**
     * 初始化当前秒数
     */
    static public function init()
    {
        self::$current = intval(date("s"));
    }

    /**
     * 设置任务
     * @param $sec_list
     * @param $task
     */
    static public function set_task($sec_list, $task)
    {
        foreach ($sec_list as $sec) {
            if (self::$cur_turntable == 1) {
                self::$turntable1[$sec][$task["id"]] = $task;

            } elseif (self::$cur_turntable == 2) {
                self::$turntable2[$sec][$task["id"]] = $task;
            }
        }
    }

    /**
     * 转换运行模式
     */
    static public function turn()
    {
        if (self::$cur_turntable == 1) {
            self::$cur_turntable = 2;
        } elseif (self::$cur_turntable == 2) {
            self::$cur_turntable = 1;
        }
    }

    /**
     * 获取当前应该执行的任务
     * @return array
     */
    static public function get_task()
    {
        $task = array();
        if (self::$cur_turntable == 1) {
            if (isset(self::$turntable2[self::$current])) {
                $task = self::$turntable2[self::$current];
                unset(self::$turntable2[self::$current]);
            }
        } elseif (self::$cur_turntable == 2) {
            if (isset(self::$turntable1[self::$current])) {
                $task = self::$turntable1[self::$current];
                unset(self::$turntable1[self::$current]);
            }
        }
        self::next_sec();
        return $task;
    }

    /**
     * 下一个任务
     */
    static protected function next_sec()
    {
        if (self::$current == 59) {
            self::$current = 0;
        } else {
            self::$current += 1;
        }
    }

    static public function debug()
    {
        var_dump(self::$turntable1);
        var_dump(self::$turntable2);
    }
}