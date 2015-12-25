<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-12-25
 * Time: 下午3:25
 */
class LoadTasksByMysql
{
    private $createTable = "
    CREATE TABLE `crontab` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `taskid` varchar(32) NOT NULL COMMENT '任务id',
  `taskname` varchar(32) NOT NULL,
  `rule` text NOT NULL COMMENT '规则 可以是crontab规则也可以是json类型的精确时间任务',
  `unique` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0 唯一任务 大于0表示同时可并行的任务进程个数',
  `execute` varchar(32) NOT NULL COMMENT '运行这个任务的类',
  `args` text NOT NULL COMMENT '任务参数',
  `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0 正常  1 暂停  2 删除',
  `createtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    ";

    protected   $db;
    protected $oriTasks;
    protected $config = array();
    public function __construct($params="")
    {
        $this->config = $this->getDbConfig();
        $this->init();
    }

    /**
     * 初始化任务表
     */
    private function init()
    {
        $db = $this->connectDB();
        $data = $db->queryOne("SELECT count(*) as total FROM information_schema.TABLES WHERE table_name = 'crontab' AND TABLE_SCHEMA = '{$this->config['dbname']}'");
        if(!empty($data) && intval($data["total"]) ==  0){
            $stmt = $db->executeSql($this->createTable);
            if($stmt){
                Main::log_write("执行sql:".$this->createTable."执行成功");
            }else{
                Main::log_write("执行sql:".$this->createTable."执行失败");
            }
        }
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