<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午5:23
 */

namespace App;


use Lib\Process;

class Exec
{
    public static function run($task)
    {
        $id = $task["id"];
        Process::create_process($id,$task);
    }
}