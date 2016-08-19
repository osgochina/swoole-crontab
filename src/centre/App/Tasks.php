<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午2:27
 */

namespace App;

use Lib\Donkeyid;
use Lib\LoadTasks;
class Tasks
{

    public static function getList()
    {
        $data = [];
        $tasks = LoadTasks::getTasks();
        foreach ($tasks as $id=>$task)
        {
            $data[$id] = $task;
        }
        return  array('data' => $data);
    }

    public static function get($id)
    {
        $tasks = LoadTasks::getTasks();
        if (!$tasks->exist($id)){
            return ["code"=>101,"msg"=>"不存在"];
        }
        $task = $tasks->get($id);
        $task["id"] = $id;
        return ["code"=>0,"data"=>$task];
    }

    public static function add($task)
    {
        $task["id"] = Donkeyid::getInstance()->dk_get_next_id();
        $ids = LoadTasks::saveTasks([$task]);
        if ($ids === false){
            return ["code"=>102,"data"=>$ids,"msg"=>"添加失败"];
        }
        return ["code"=>0,"data"=>$ids,"msg"=>"保存成功"];
    }

    public static function update($id,$task)
    {
        if (!LoadTasks::updateTask($id,$task)){
            return ["code"=>102,"msg"=>"更新失败"];
        }
        return ["code"=>0,"data"=>'',"msg"=>"更新成功"];
    }

    public static function delete($id)
    {
        if (!LoadTasks::delTask($id)){
            return ["code"=>102,"msg"=>"删除失败"];
        }
        return ["code"=>0,"data"=>'',"msg"=>"删除成功"];
    }

}