<?php

/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-18
 * Time: 下午4:52
 */
namespace App;
use Lib\LoadTasks;

class Cron
{
    public static function index()
    {
        $data = [];
        $tasks = LoadTasks::getTasks();
        foreach ($tasks as $id=>$task)
        {
            $data[$id] = $task;
        }
        return  array('data' => $data);
    }
}