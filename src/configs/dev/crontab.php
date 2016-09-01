<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午1:15
 */


return [
    "load_size"=>"8192",//最多载入任务数量
    "tasks_size"=>"1024",//同时运行任务最大数量
    "robot_num_max"=>"8",//同时挂载worker数量
    "robot_process_max"=>"128",//单个worker同时执行任务数量
    "centre_host"=>"127.0.0.1",//中心服ip
    "centre_port"=>"8901",//中心服端口
];