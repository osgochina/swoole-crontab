<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午5:23
 */

namespace App;
use Lib;
class Exec
{
    /**
     * 中心服通知worker需要运行任务
     * @param $task
     * @return bool
     */
    public static function run($task)
    {
        return Lib\Process::create_process($task);
    }
}