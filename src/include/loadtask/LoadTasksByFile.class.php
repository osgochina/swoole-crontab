<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 下午3:46
 */
class LoadTasksByFile
{
    protected $filePath;
    protected $oriTasks;
    protected $tasks = array();

    public function __construct($file)
    {
        if(empty($file) || (!empty($file) && !file_exists($file))){
            Main::log_write("指定配置文件不存在,file:".$file);
            exit;
        }
        $this->filePath = $file;
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
        $this->loadTasks();
        $this->config = $this->parseTasks();
    }

    /**
     * 从配置文件载入配置
     */
    protected function loadTasks()
    {
        $this->oriTasks = include($this->filePath);
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
                $tasks[$key] = array(
                    "name" => $val["name"],
                    "time" => $val["time"],
                    "unique" => $val["unique"],
                    "parse" => $val["parse"],
                    "task" => $val["task"]
                );
            }
        }
        return $tasks;
    }
}