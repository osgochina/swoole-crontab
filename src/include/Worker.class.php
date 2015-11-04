<?php

/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-4
 * Time: 下午10:16
 */
class Worker
{

    public function loadWorker(){

    }

    protected function getWorkers(){
        $path = ROOT_PATH."config/worker.php";
        $config = include $path;
        if(empty($config)){
            return;
        }
        foreach($config as $name=>$task){

        }
    }

}