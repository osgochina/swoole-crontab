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
        LoadConfig::reload_config();
        return $this->output("ok");
    }

    function addcrontab_cron($params)
    {
        $tasks = $params["post"]["tasks"];
        $tasks = json_decode($tasks, true);
        if (empty($tasks)) {
            return $this->output("参数有误", false);
        }
        foreach ($tasks as $id => $task) {
            if (empty($task["name"]) || empty($task["time"]) || empty($task["task"])) {
                return $this->output("参数有误", false);
            }
        }
        LoadConfig::send_config($tasks);
        LoadConfig::reload_config();
        return $this->output("ok");
    }

    function delcrontab_cron($params)
    {
        $task = $params["get"]["taskid"];
        if (!is_string($task)) {
            return $this->output("参数有误", false);
        }
        LoadConfig::del_config($task);
        LoadConfig::reload_config();
        return $this->output("ok");
    }

    /**
     * @param $request
     * @return array|string
     */
    function loglist_http($request)
    {
        $date = $request->get["date"];
        if ($date) {
            $filename = ROOT_PATH . "logs/log_" . $date . ".log";
            $data = file_get_contents($filename);
            $data = $this->output($data);
        } else {
            $data = $this->output("参数有误", false);
        }
        return $data;
    }

    /**
     * 导入任务配置数据，会清空存在的数据
     * @param $request
     * @return array
     */
    function importconf_http($request)
    {
        $tasks = $request->post["tasks"];
        $tasks = json_decode($tasks, true);
        if (empty($tasks)) {
            return $this->output("参数有误", false);
        }
        foreach ($tasks as $id => $task) {
            if (empty($task["name"]) || empty($task["time"]) || empty($task["task"])) {
                return $this->output("参数有误", false);
            }
        }
        ob_start();
        var_export($tasks);
        $config = ob_get_clean();
        file_put_contents(Http::$conf_file, "<?php \n return " . $config . ";");
        fwrite(Http::$fp, "reloadconf#@#" . json_encode(array()));

        return $this->output("ok");
    }

    public function output($data, $status = true)
    {
        return array("status" => $status, "data" => $data);
    }
}