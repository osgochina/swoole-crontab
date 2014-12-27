<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 14-12-27
 * Time: 下午3:13
 */
class  Gearman implements PluginBase
{

    public function run($task)
    {
        Main::log_write($task["function"]);
    }
}