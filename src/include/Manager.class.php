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
        return $this->output(LoadConfig::get_config());
    }

    function addcrontab_cron($params)
    {
        $tasks = $params["post"]["tasks"];
        $tasks = json_decode($tasks,true);
        if(empty($tasks)){
            return $this->output("参数有误",false);
        }
        foreach($tasks as $id=>$task){
            if(empty($task["name"]) || empty($task["time"]) || empty($task["task"])){
                return $this->output("参数有误",false);
            }
        }
        LoadConfig::send_config($tasks);
        Crontab::load_config(true);
        return $this->output("ok");
    }

    function delcrontab_cron($params)
    {
        $task = $params["get"]["taskid"];
        if(!is_string($task)){
            return $this->output("参数有误",false);
        }
        LoadConfig::del_config($task);
        Crontab::load_config(true);
        return $this->output("ok");
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
            $data = $this->output($data);
        }else{
            $data = $this->output("参数有误",false);
        }
        $response->end($data);
    }

    public function output($data,$status=true)
    {
        return array("status"=>$status,"data"=>$data);
    }
}