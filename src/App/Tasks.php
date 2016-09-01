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

    /**
     * 获取任务列表
     * @return array
     */
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

    /**
     * 获取单个任务
     * @param $id
     * @return array
     */
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

    /**
     * 添加任务
     * @param $task
     * @return array
     */
    public static function add($task)
    {
        $task["id"] = Donkeyid::getInstance()->dk_get_next_id();
        $ids = LoadTasks::saveTasks([$task]);
        if ($ids === false){
            return ["code"=>102,"data"=>$ids,"msg"=>"添加失败"];
        }
        return ["code"=>0,"data"=>$ids,"msg"=>"保存成功"];
    }

    /**
     *  修改任务
     * @param $id
     * @param $task
     * @return array
     */
    public static function update($id,$task)
    {
        if (!LoadTasks::updateTask($id,$task)){
            return ["code"=>102,"msg"=>"更新失败"];
        }
        return ["code"=>0,"data"=>'',"msg"=>"更新成功"];
    }

    /**
     * 删除任务
     * @param $id
     * @return array
     */
    public static function delete($id)
    {
        if (!LoadTasks::delTask($id)){
            return ["code"=>102,"msg"=>"删除失败"];
        }
        return ["code"=>0,"data"=>'',"msg"=>"删除成功"];
    }

}