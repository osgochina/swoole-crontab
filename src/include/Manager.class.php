<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-1-8
 * Time: 下午9:31
 */

class Manager
{

    /**
     * @param $params
     * @return array
     */
    function getcrontab_cron($params)
    {
        return LoadConfig::get_config();
    }

    function addcrontab_cron($params)
    {
        $tasks = $params["post"]["tasks"];
        $tasks = json_decode($tasks,true);
        LoadConfig::send_config($tasks);
        Crontab::load_config(true);
        return "ok";
    }

    function delcrontab_cron($params)
    {
        $task = $params["get"]["task"];
        LoadConfig::del_config($task);
        Crontab::load_config(true);
        return "ok";
    }

    /**
     * @param $params
     */
    function loglist_http($request,$response)
    {
        $date = $request->get["date"];
        if($date){
            $filename = ROOT_PATH."logs/log_".$date.".log";
            $data = file_get_contents($filename);
        }else{
            $data = "参数有误";
        }
        $response->end($data);
    }
}