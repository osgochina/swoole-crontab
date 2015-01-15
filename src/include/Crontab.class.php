<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-28
 * Time: 下午2:03
 */
class Crontab
{
    static public $process_name = "lzm_Crontab";//进程名称
    static public $pid_file;                    //pid文件位置
    static public $log_path;                    //日志文件位置
    static public $config_file;                 //配置文件位置
    static public $daemon = false;              //运行模式
    static private $pid;                        //pid
    static public $checktime = false;           //精确对时
    static public $task_list = array();
    static public $unique_list = array();

    /**
     * 重启
     */
    static public function restart()
    {
        self::stop(false);
        sleep(1);
        self::start();
    }

    /**
     * 停止进程
     * @param bool $output
     */
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

    /**
     * 启动
     */
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

    /**
     * 匹配运行模式
     */
    static private function daemon()
    {
        if (self::$daemon) {
            swoole_process::daemon();
        }
    }

    /**
     * 设置进程名
     */
    static private function set_process_name()
    {
        if (!function_exists("swoole_set_process_name")) {
            self::exit2p("Please install swoole extension.http://www.swoole.com/");
        }
        swoole_set_process_name(self::$process_name);
    }

    /**
     * 退出进程口
     * @param $msg
     */
    static private function exit2p($msg)
    {
        @unlink(self::$pid_file);
        Main::log_write($msg . "\n");
        exit();
    }

    /**
     * 运行
     */
    static protected function run()
    {
        self::get_pid();
        self::write_pid();
        LoadConfig::$config_file = self::$config_file;
        TurnTable::init();
        self::load_config();
        self::register_signal();
        if (self::$checktime) {
            $run = true;
            while ($run) {
                if (date("s") == 0) {
                    Crontab::load_config();
                    self::register_timer();
                    $run = false;
                }
                sleep(1);
            }
        } else {
            self::register_timer();
        }

    }

    /**
     * 过去当前进程的pid
     */
    static private function get_pid()
    {
        if (!function_exists("posix_getpid")) {
            self::exit2p("Please install posix extension.");
        }
        self::$pid = posix_getpid();
    }

    /**
     * 写入当前进程的pid到pid文件
     */
    static private function write_pid()
    {
        file_put_contents(self::$pid_file, self::$pid);
    }

    /**
     * 根据配置载入需要执行的任务
     */
    static public function load_config($reload = false)
    {
        $time = time();
        if ($reload) {
            LoadConfig::reload_config();
            TurnTable::init();
        }
        $config = LoadConfig::get_config();
        foreach ($config as $id => $task) {
            //如果任务唯一性，则不执行任务
            if(isset(self::$unique_list[$id])){
                continue;
            }
            $ret = ParseCrontab::parse($task["time"], $time);
            if ($ret === false) {
                Main::log_write(ParseCrontab::$error);
            } elseif (!empty($ret)) {
                TurnTable::set_task($ret, array_merge($task, array("id" => $id)));
            }
        }
        TurnTable::turn();
    }

    /**
     *  注册定时任务
     */
    static protected function register_timer()
    {
        swoole_timer_add(60000, function ($interval) {
            Crontab::load_config();
        });
        swoole_timer_add(1000, function ($interval) {
            Crontab::do_something($interval);
        });
    }

    /**
     * 运行任务
     * @param $interval
     * @return bool
     */
    static public function do_something($interval)
    {
        //TurnTable::debug();
        $tasks = TurnTable::get_task();
        if (empty($tasks)) return false;
        foreach ($tasks as $id => $task) {
            if(isset(self::$unique_list[$id])){
                continue;
            }
            if(isset($task["unique"]) && $task["unique"]){
                self::$unique_list[$id] = true;
            }
            (new Process())->create_process($id, $task);
        }
        return true;
    }

    /**
     * 注册信号
     */
    static private function register_signal()
    {
        swoole_process::signal(SIGTERM, function ($signo) {
            if (!empty(Main::$http_server)) {
                swoole_process::kill(Main::$http_server->pid, SIGKILL);
            }
            self::exit2p("收到退出信号,退出主进程");
        });
        swoole_process::signal(SIGCHLD, function ($signo) {
            while (($pid = pcntl_wait($status, WNOHANG)) > 0) {
                $task = self::$task_list[$pid];
                $end = microtime(true);
                $start = $task["start"];
                $id = $task["id"];
                Main::log_write("{$id} [Runtime:" . sprintf("%0.6f", $end - $start) . "]");
                unset(self::$task_list[$pid]);
                unset(self::$unique_list[$id]);
            };
        });
        swoole_process::signal(SIGUSR1, function ($signo) {
            Crontab::load_config(true);
        });

    }
}