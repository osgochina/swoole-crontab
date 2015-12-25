<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-12-25
 * Time: 下午3:04
 */
class LoadTasks
{
    private $handle;
    public function __construct($type,$params="")
    {
        switch($type){
            case "file":
                $this->handle =  new LoadTasksByFile($params);
                break;
            case "mysql":
                $this->handle =  new LoadTasksByMysql($params);
                break;
            default:
                $this->handle =  new LoadTasksByFile($params);
                break;
        }
    }

    /**
     * 获取需要执行的任务
     * @return array
     */
    public function getTasks()
    {
        return $this->handle->getTasks();
    }

    /**
     * 重载任务配置
     */
    public function reloadTasks()
    {
        $this->handle->reloadTasks();
    }
}