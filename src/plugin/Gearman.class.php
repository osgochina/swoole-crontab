<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
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