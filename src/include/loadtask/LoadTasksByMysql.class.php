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
    protected $config = array();
    public function __construct($params="")
    {
        $this->config = $this->getDbConfig();
    }

    /**
     * 返回格式化好的任务配置
     * @return array
     */
    public function getTasks()
    {
        $this->loadTasks();
        return self::parseTasks();
    }

    /**
     * 从配置文件载入配置
     */
    protected function loadTasks()
    {
        echo "reload\n";
        $db =$this->connectDB();
        $data = $db->queryAll("select * from `crontab` where `status`=0");
        $db = null;
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
        return new EasyDB($this->config);
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