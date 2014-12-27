<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 上午11:59
 */


date_default_timezone_set('Asia/Shanghai');
define('APP_DEBUG', true);
define('APP_ENVIRONMENT', 'dev');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS);
spl_autoload_register(function($name){
    $file_path= ROOT_PATH."include".DS.$name.".class.php";
    include $file_path;
});

class Main
{
    static public function register_timer()
    {
        swoole_timer_add(60000,function($interval){
            Main::load_config($interval);
        });
        swoole_timer_add(1000,function($interval){
            Main::do_something($interval);
        });
    }

    static public function load_config()
    {
        $time = time();
        $config = LoadConfig::get_config();
        foreach($config as $id=>$task){
            $ret = ParseCrontab::parse($task["time"],$time);
            if($ret === false){
                self::log_write(ParseCrontab::$error);
            }elseif(!empty($ret)){
                TurnTable::set_task($ret,array_merge($task,array("id"=>$id)));
            }
        }
        TurnTable::turn();
    }
    static public function do_something($interval)
    {
       var_dump(TurnTable::get_task());
    }

    static public function log_write($message)
    {
        $now = date("H:i:s");
//        if ($this->daemon) {
//            $destination = $this->log_path . "log_" . date("Y-m-d") . ".log";
//            error_log("{$now} : {$message}\r\n", 3, $destination, '');
//        }
        echo "{$now} : {$message}\r\n";
    }

    static public function run()
    {
        LoadConfig::$config_path = ROOT_PATH."config".DS.APP_ENVIRONMENT.DS;
        TurnTable::init();
        self::load_config();
        self::register_timer();
    }
}

Main::run();

