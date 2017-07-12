<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 2017/7/10
 * Time: 23:39
 */

namespace Lib;

class Server
{
    protected static $options = array();
    protected static $beforeStopCallback;
    protected static $beforeReloadCallback;

    public $server;

    /**
     * @var \Swoole\Client
     */
    protected $sw;
    protected $processName;

    static $pidFile;
    static $optionKit;

    static $defaultOptions = array(
        'd|daemon' => '启用守护进程模式',
        'h|host:' => '中心服host',
        'p|port:' => '中心服port',
        'l|log?' => 'log文件地址',
        'pid?' => 'pid文件地址',
        'help' => '显示帮助界面',
    );

    protected $host;
    protected $port;
    protected $flag;


    /**
     * 设置PID文件
     * @param $pidFile
     */
    static function setPidFile($pidFile)
    {
        self::$pidFile = $pidFile;
    }

    /**
     * @param callable $function
     */
    static function beforeStop(callable $function)
    {
        self::$beforeStopCallback = $function;
    }

    /**
     * @param callable $function
     */
    static function beforeReload(callable $function)
    {
        self::$beforeReloadCallback = $function;
    }



    /**
     * 显示命令行指令
     */
    static function start($startFunction)
    {
        if (!self::$optionKit) {
            Loader::addNameSpace('GetOptionKit', WEBPATH."/Lib/GetOptionKit/src/GetOptionKit");
            self::$optionKit = new \GetOptionKit\GetOptionKit;
        }

        $kit = self::$optionKit;
        foreach(self::$defaultOptions as $k => $v) {
            //解决Windows平台乱码问题
            if (PHP_OS == 'WINNT') {
                $v = iconv('utf-8', 'gbk', $v);
            }
            $kit->add($k, $v);
        }
        global $argv;
        $opt = $kit->parse($argv);
        if (isset($opt['pid'])){
            self::$pidFile = $opt['pid'];
        }
        if (empty(self::$pidFile)) {
            throw new \Exception("require pidFile.");
        }
        $pid_file = self::$pidFile;
        if (is_file($pid_file)) {
            $server_pid = file_get_contents($pid_file);
        } else {
            $server_pid = 0;
        }

        if (empty($argv[1]) or isset($opt['help'])) {
            goto usage;
        } elseif ($argv[1] == 'stop') {
            if (empty($server_pid)) {
                exit("Server is not running\n");
            }
            if (self::$beforeStopCallback) {
                call_user_func(self::$beforeStopCallback, $opt);
            }
            posix_kill($server_pid, SIGTERM);
            exit;
        } elseif ($argv[1] == 'start') {
            //已存在ServerPID，并且进程存在
            if (!empty($server_pid) and posix_kill($server_pid, 0))
            {
                exit("Server is already running.\n");
            }
        } else {
            usage:
            $kit->specs->printOptions("php {$argv[0]} start|stop");
            exit;
        }
        self::$options = $opt;
        $startFunction($opt);
    }

    /**
     * 自动创建对象
     * @return Server
     */
    static function autoCreate($host, $port, $ssl = false)
    {

        return new self($host, $port, $ssl);
    }
    protected static function setProcessTitle($title)
    {
        // >=php 5.5
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } // Need proctitle when php<=5.5 .
        elseif (extension_loaded('proctitle') && function_exists('setproctitle')) {
            @setproctitle($title);
        }
    }

    public function __construct($host, $port, $ssl = false)
    {
        $this->flag = $ssl ? (SWOOLE_SOCK_TCP | SWOOLE_SSL) : SWOOLE_SOCK_TCP;
        $this->host = $host;
        $this->port = $port;
    }

    function run($setting)
    {
        if (!empty(self::$options['daemon'])) {
            \swoole_process::daemon();
        }
        if (!empty($this->processName)){
            self::setProcessTitle($this->processName." host:".$this->host." port:".$this->port);
        }
        $this->sw = new \swoole_client($this->flag,SWOOLE_SOCK_ASYNC);
        $this->sw->on("Connect",[$this,"_onConnect"]);
        $this->sw->on("Error",[$this,"_onError"]);
        $this->sw->on("Receive",[$this,"_onReceive"]);
        $this->sw->on("Close",[$this,"_onClose"]);
        $this->sw->set($setting);
        $this->connect();
    }

    public function _onConnect($client)
    {
        if (is_callable([$this->server,'onConnect'])){
            call_user_func([$this->server,'onConnect'], $client);
        }
    }

    public function _onError($client)
    {
        if (is_callable([$this->server,'onError'])){
            call_user_func([$this->server,'onError'], $client);
        }
    }

    public function _onReceive($client, $data)
    {
        if (is_callable([$this->server,'onReceive'])){
            if (!is_callable(["\\Lib\\SOAProtocol",'decode'])){
                throw new \Exception("protocol not found");
            }
            $data = SOAProtocol::decode($data);
            call_user_func([$this->server,'onReceive'], $client,$data);
        }
    }
    public function _onClose($client)
    {
        if (is_callable([$this->server,'onClose'])){
            call_user_func([$this->server,'onClose'], $client);
        }
    }

    public function connect()
    {
        if ($this->sw->isConnected()){
            return true;
        }
        return $this->sw->connect($this->host,$this->port,30);
    }

    public function call()
    {
        if (!$this->sw->isConnected()){
            return false;
        }
        $args = func_get_args();
        $function = $args[0];
        $params = array_slice($args, 1);
        $env = array_slice($args, 2);

        if (!is_callable(["\\Lib\\SOAProtocol",'encode'])){
            throw new \Exception("protocol not found");
        }
        return $this->sw->send(SOAProtocol::encode($function, $params, $env));
    }

    public function getError()
    {
        return [
            'code'=>$this->sw->errCode,
            'message'=>socket_strerror($this->sw->errCode),
        ];
    }

    public function getRemoteIp()
    {

    }

    public function getRemotePort()
    {

    }

    function setServer($server)
    {
        $this->server = $server;
        $server::$client = $this;
    }

    /**
     * 设置进程名称
     * @param $name
     */
    function setProcessName($name)
    {
        $this->processName = $name;
    }


    public function close()
    {
        if ($this->sw){
            $this->sw->close(true);
        }
    }

}