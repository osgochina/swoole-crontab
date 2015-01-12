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

    /**
     * 重新加载配置文件
     * @param $params
     * @return array
     */
    function reloadconf_cron($params)
    {
        Crontab::load_config(true);
        return $this->output("ok");
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
        $response->end(json_encode($data));
    }

    function importconf_http($request,$response)
    {
        $tasks = $request->post["tasks"];
        $tasks = json_decode($tasks,true);
        if(empty($tasks)){
            $response->end(json_encode($this->output("参数有误",false)));
        }
        foreach($tasks as $id=>$task){
            if(empty($task["name"]) || empty($task["time"]) || empty($task["task"])){
                $response->end(json_encode($this->output("参数有误",false)));
            }
        }
        ob_start();
        var_export($tasks);
        $config = ob_get_clean();
        file_put_contents(ROOT_PATH."config/conf.php","<?php \n return ".$config.";");
        fwrite(Http::$fp,"reloadconf#@#".json_encode(array()));

        $response->end(json_encode($this->output("ok")));
    }

    public function output($data,$status=true)
    {
        return array("status"=>$status,"data"=>$data);
    }
}