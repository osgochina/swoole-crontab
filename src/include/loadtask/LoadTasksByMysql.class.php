<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-12-25
 * Time: 下午3:25
 */
class LoadTasksByMysql
{
    protected   $db;
    protected $oriTasks;
    protected $tasks = array();
    public function __construct($params="")
    {
        $this->connectDB();
    }

    /**
     * 返回格式化好的任务配置
     * @return array
     */
    public function getTasks()
    {
        if (empty($this->tasks)) {
            $this->loadTasks();
            $this->tasks = self::parseTasks();
        }

        return $this->tasks;
    }

    public function reloadTasks()
    {
        $this->tasks = array();
        $this->loadTasks();
        $this->config = $this->parseTasks();
    }

    /**
     * 从配置文件载入配置
     */
    protected function loadTasks()
    {
        $data = $this->db->query("`status`=0")->select("`crontab`");
        $this->oriTasks = $data;
    }

    /**
     * 格式化配置文件中的配置
     * @return array
     */
    protected function parseTasks()
    {
        $tasks = array();
        if (is_array($this->oriTasks)) {
            foreach ($this->oriTasks as $key => $val) {
                $rule = json_decode($val["rule"],true);
                if(!is_array($rule)){
                    $rule = $val["rule"];
                }
                $tasks[$val["taskid"].$val["id"]] = array(
                    "name" => $val["taskname"],
                    "time" => $rule,
                    "unique" => $val["unique"],
                    "parse" => $val["execute"],
                    "task" => json_decode($val["args"],true)
                );
            }
        }
        return $tasks;
    }

    protected function connectDB()
    {
        $this->db = new EasyDB($this->getDbConfig());
    }

    protected function getDbConfig(){
        $config = include( ROOT_PATH . "config/config.php");
        if(empty($config) || !isset($config["mysql"])){
            Main::log_write("mysql config not found");
            exit();
        }
        return $config["mysql"];
    }

}