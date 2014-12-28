<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-28
 * Time: 下午2:03
 */

class Crontab
{
    static public $process_name = "lzm_Crontab";
    static private $pid;
    static public $pid_file;
    static public $log_path;
    static public $config_file;
    static public $daemon = false;

    static public function register_timer()
    {
        swoole_timer_add(60000,function($interval){
            Crontab::load_config($interval);
        });
        swoole_timer_add(1000,function($interval){
            Crontab::do_something($interval);
        });
    }

    static public function load_config()
    {
        $time = time();
        $config = LoadConfig::get_config();
        foreach($config as $id=>$task){
            $ret = ParseCrontab::parse($task["time"],$time);
            if($ret === false){
                Main::log_write(ParseCrontab::$error);
            }elseif(!empty($ret)){
                TurnTable::set_task($ret,array_merge($task,array("id"=>$id)));
            }
        }
        TurnTable::turn();
    }
    static public function do_something($interval)
    {
        $tasks = TurnTable::get_task();
        if(empty($tasks)) return false;
        foreach($tasks as $task){
            (new Process())->create_process($task);
        }
        return true;
    }



    static public function run()
    {
        self::get_pid();
        self::write_pid();
        LoadConfig::$config_file = self::$config_file;
        TurnTable::init();
        self::load_config();
        self::register_timer();
        self::register_signal();
    }

    static private function register_signal()
    {
        swoole_process::signal(SIGTERM, function ($signo) {
            self::exit2p("收到退出信号,退出主进程");
        });
    }

    static  private function daemon()
    {
        if (self::$daemon) {
            swoole_process::daemon();
        }
    }

    static private function set_process_name()
    {
        if (!function_exists("swoole_set_process_name")) {
            self::exit2p("Please install swoole extension.http://www.swoole.com/");
        }
        swoole_set_process_name(self::$process_name);
    }

    static public function start()
    {
        if (file_exists(self::$pid_file)) {
            die("Pid文件已存在!\n");
        }
        self::daemon();
        self::set_process_name();
        self::run();
        Main::log_write("启动成功");
    }
    static public function stop($output = true)
    {
        $pid = @file_get_contents(self::$pid_file);
        if ($pid) {
            if (swoole_process::kill($pid, 0)) {
                swoole_process::kill($pid, SIGTERM);
                Main::log_write("进程" . $pid . "已结束");
            } else {
                @unlink(self::$pid_file);
                Main::log_write("进程" . $pid . "不存在,删除pid文件");
            }
        } else {
            $output && Main::log_write("需要停止的进程未启动");
        }
    }

    static public function restart()
    {
        self::stop(false);
        sleep(1);
        self::start();
    }
    static private function get_pid()
    {
        if (!function_exists("posix_getpid")) {
            self::exit2p("Please install posix extension.");
        }
        self::$pid = posix_getpid();
    }

    static private function write_pid()
    {
        file_put_contents(self::$pid_file, self::$pid);
    }



    static private function exit2p($msg)
    {
        @unlink(self::$pid_file);
        Main::log_write($msg . "\n");
        exit();
    }
}