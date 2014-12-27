<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 14-12-27
 * Time: 下午2:46
 */

class TurnTable
{
    static protected $turntable1 = array();
    static protected $turntable2 = array();
    static protected $cur_turntable = 1;
    static protected $current = 0;

    static public function init()
    {
        self::$current = intval(date("s"));
    }

    static public function set_task($sec_list,$task)
    {
        foreach($sec_list as $sec){
            if(self::$cur_turntable == 1){
                self::$turntable1[$sec][$task["id"]] = $task;
                self::$cur_turntable = 2;
            }elseif(self::$cur_turntable == 2){
                self::$turntable2[$sec][$task["id"]] = $task;
                self::$cur_turntable = 1;
            }
        }
    }

    static public function get_task()
    {
        $task = array();
        if(self::$cur_turntable == 1){
            $task = isset(self::$turntable2[self::$current])?self::$turntable2[self::$current]:array();
        }elseif(self::$cur_turntable == 2){
            $task = isset(self::$turntable1[self::$current])?self::$turntable1[self::$current]:array();
        }
        self::next_sec();
        return $task;
    }

    static protected function next_sec()
    {
        if(self::$current == 59){
            self::$current = 0;
        }else{
            self::$current+=1;
        }
    }
}