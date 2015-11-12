<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午3:13
 */
class  Gearman extends  PluginBase
{


    public function run($task)
    {
        $client = new GearmanClient();
        $client->addServers($task["server"]);
        $client->doBackground($task["cmd"], $task["ext"]);
        if (($code = $client->returnCode()) != GEARMAN_SUCCESS)
        {
            Main::log_write("Gearman:".$task["cmd"]." to ".$task["server"]." error,code=".$code);
            exit;
        }
        Main::log_write("Gearman:".$task["cmd"]." to ".$task["server"]." success,code=".$code);
        exit;

    }
}