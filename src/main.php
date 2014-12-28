<?php
/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-28
 * Time: 下午1:55
 */

date_default_timezone_set('Asia/Shanghai');
define('APP_DEBUG', true);
define('APP_ENVIRONMENT', 'dev');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS);

class Main
{

    static private $options = "hdp:s:l:c:";
    static private $longopts = array("help", "daemon", "pid:", "log:", "config:");
    static private $help = <<<EOF

  帮助信息:
  Usage: /path/to/php main.php [options] -- [args...]

  -h [--help]        显示帮助信息
  -p [--pid]         指定pid文件位置(默认pid文件保存在当前目录)
  -s start           启动进程
  -s stop            停止进程
  -s restart         重启进程
  -l [--log]         log文件夹的位置
  -c [--config]      config文件的位置(可以是文件,也可以是文件夹.
                     如果是文件,则载入指定文件.如果是文件夹,则载入文件夹
                     下的所有文件.)
  -d [--daemon]      是否后台运行

EOF;

    /**
     * 运行入口
     */
    static public function run()
    {
        $opt = getopt(self::$options, self::$longopts);
        self::spl_autoload_register();
        self::params_h($opt);
        self::params_d($opt);
        self::params_p($opt);
        self::params_l($opt);
        self::params_c($opt);
        self::params_s($opt);
    }

    /**
     * 注册类库载入路径
     */
    static public function spl_autoload_register()
    {
        spl_autoload_register(function ($name) {
            $file_path = ROOT_PATH . "include" . DS . $name . ".class.php";
            include $file_path;
        });
    }

    /**
     * 解析帮助参数
     * @param $opt
     */
    static public function params_h($opt)
    {
        if (empty($opt) || isset($opt["h"]) || isset($opt["help"])) {
            die(self::$help);
        }
    }

    /**
     * 解析运行模式参数
     * @param $opt
     */
    static public function params_d($opt)
    {
        if (isset($opt["d"]) || isset($opt["daemon"])) {
            Crontab::$daemon = true;
        }
    }

    /**
     * 解析pid参数
     * @param $opt
     */
    static public function params_p($opt)
    {
        //记录pid文件位置
        if (isset($opt["p"]) && $opt["p"]) {
            Crontab::$pid_file = $opt["p"] . "/pid";
        }
        //记录pid文件位置
        if (isset($opt["pid"]) && $opt["pid"]) {
            Crontab::$pid_file = $opt["pid"] . "/pid";
        }
        if (empty(Crontab::$pid_file)) {
            Crontab::$pid_file = ROOT_PATH . "/pid";
        }
    }

    /**
     * 解析日志路径参数
     * @param $opt
     */
    static public function params_l($opt)
    {
        if (isset($opt["l"]) && $opt["l"]) {
            Crontab::$log_path = $opt["l"];
        }
        if (isset($opt["log"]) && $opt["log"]) {
            Crontab::$log_path = $opt["log"];
        }
        if (empty(Crontab::$log_path)) {
            Crontab::$log_path = ROOT_PATH . "/logs/";
        }
    }

    /**
     * 解析配置文件位置参数
     * @param $opt
     */
    static public function params_c($opt)
    {
        if (isset($opt["c"]) && $opt["c"]) {
            Crontab::$config_file = $opt["c"];
        }
        if (isset($opt["config"]) && $opt["config"]) {
            Crontab::$config_file = $opt["config"];
        }
        if (empty(Crontab::$config_file)) {
            Crontab::$config_file = ROOT_PATH . "config/" . APP_ENVIRONMENT . "/";
        }
    }

    /**
     * 解析启动模式参数
     * @param $opt
     */
    static public function params_s($opt)
    {
        //判断传入了s参数但是值，则提示错误
        if ((isset($opt["s"]) && !$opt["s"]) || (isset($opt["s"]) && !in_array($opt["s"], array("start", "stop", "restart")))) {
            Crontab::log_write("Please run: path/to/php main.php -s [start|stop|restart]");
        }

        if (isset($opt["s"]) && in_array($opt["s"], array("start", "stop", "restart"))) {
            switch ($opt["s"]) {
                case "start":
                    Crontab::start();
                    break;
                case "stop":
                    Crontab::stop();
                    break;
                case "restart":
                    Crontab::restart();
                    break;
            }
        }
    }

    /**
     * 记录日志
     * @param $message
     */
    static public function log_write($message)
    {
        $now = date("H:i:s");
        if (Crontab::$daemon) {
            $destination = Crontab::$log_path . "log_" . date("Y-m-d") . ".log";
            error_log("{$now} : {$message}\r\n", 3, $destination, '');
        }
        echo "{$now} : {$message}\r\n";
    }


}
//运行
Main::run();