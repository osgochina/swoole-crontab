<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 16-1-25
 * Time: 下午9:49
 */
class TickTable extends SplHeap
{
    public static $Instance;
    public static function getInstance(){
        if(!self::$Instance){
            self::$Instance = new TickTable();
        }
        return self::$Instance;
    }
    protected function compare($val1,$val2){
        if($val1["tick"] === $val2["tick"]) return 0;
        return $val1["tick"] < $val2["tick"]?1:-1;
    }

    public static function set_task($sec_list,$task){
        $time = time();
        foreach ($sec_list as $sec) {
            if($sec > 60){
                self::getInstance()->insert(array("tick"=>$sec,"task"=>$task));
            }else{
                self::getInstance()->insert(array("tick"=>$time+$sec,"task"=>$task));
            }
        }
    }

    public static function get_task(){
        $time = time();
        $ticks = array();
        while(self::getInstance()->valid()){
            $data = self::getInstance()->extract();
            if($data["tick"] > $time){
                self::getInstance()->insert($data);
                break;
            }else{
                $ticks[] = $data["task"];
            }
        }
        return $ticks;
    }
}

