<?php
namespace Lib;

/**
 * Class Server
 * @package Swoole\Network
 */
class SOAServer
{

    public $log;
    public $server;
    protected $clients;

    protected $_buffer  = array(); //buffer区
    protected $_headers = array(); //保存头

    protected $errCode;
    protected $errMsg;

    /**
     * 客户端环境变量
     * @var array
     */
    static $clientEnv;
    static $stop = false;

    /**
     * 请求头
     * @var array
     */
    static $requestHeader;

    public $packet_maxlen       = 2465792; //2M默认最大长度
    protected $buffer_maxlen    = 10240; //最大待处理区排队长度, 超过后将丢弃最早入队数据
    protected $buffer_clear_num = 128; //超过最大长度后，清理100个数据

    const ERR_HEADER            = 9001;   //错误的包头
    const ERR_TOOBIG            = 9002;   //请求包体长度超过允许的范围
    const ERR_SERVER_BUSY       = 9003;   //服务器繁忙，超过处理能力

    const ERR_UNPACK            = 9204;   //解包失败
    const ERR_PARAMS            = 9205;   //参数错误
    const ERR_NOFUNC            = 9206;   //函数不存在
    const ERR_CALL              = 9207;   //执行错误

    const HEADER_SIZE           = 16;
    const HEADER_STRUCT         = "Nlength/Ntype/Nuid/Nserid";
    const HEADER_PACK           = "NNNN";

    const DECODE_PHP            = 1;   //使用PHP的serialize打包
    const DECODE_JSON           = 2;   //使用json_encode打包
    const DECODE_GZIP           = 128; //启用GZIP压缩

    protected $appNamespaces    = array(); //应用程序命名空间


    /**
     * 设置Logger
     * @param $log
     */
    function setLogger($log)
    {
        $this->log = $log;
    }

    function run($array)
    {
        //\Swoole\Error::$echo_html = true;
        $this->server->run($array);
    }

    function daemonize()
    {
        $this->server->daemonize();
    }

    /**
     * 打印Log信息
     * @param $msg
     * @param string $type
     */
    function log($msg)
    {
        $this->log->info($msg);
    }

    function task($task, $dstWorkerId = -1, $callback = null)
    {
        $this->server->task($task, $dstWorkerId = -1, $callback);
    }

    function onStart($server)
    {

    }

    function onConnect($server, $client_id, $from_id)
    {

    }


    function onWorkerStop($serv, $worker_id)
    {
        $this->log("Worker[$worker_id] is stop");
    }

    function onShutdown($server)
    {
        $this->log("Worker is stop");
        Flog::flush();
    }

    function onTimer($serv, $interval)
    {
        $this->log("Timer[$interval] call");
    }

    function onReceive($serv, $fd, $from_id, $data)
    {
        if (!isset($this->_buffer[$fd]) or $this->_buffer[$fd] === '')
        {
            //超过buffer区的最大长度了
            if (count($this->_buffer) >= $this->buffer_maxlen)
            {
                $n = 0;
                foreach ($this->_buffer as $k => $v)
                {
                    $this->close($k);
                    $n++;
                    //清理完毕
                    if ($n >= $this->buffer_clear_num)
                    {
                        break;
                    }
                }
                $this->log("clear $n buffer");
            }
            //解析包头
            $header = unpack(self::HEADER_STRUCT, substr($data, 0, self::HEADER_SIZE));
            //错误的包头
            if ($header === false)
            {
                $this->close($fd);
            }
            $header['fd'] = $fd;
            $this->_headers[$fd] = $header;
            //长度错误
            if ($header['length'] - self::HEADER_SIZE > $this->packet_maxlen or strlen($data) > $this->packet_maxlen)
            {
                return $this->sendErrorMessage($fd, self::ERR_TOOBIG);
            }
            //加入缓存区
            $this->_buffer[$fd] = substr($data, self::HEADER_SIZE);
        }
        else
        {
            $this->_buffer[$fd] .= $data;
        }

        //长度不足
        if (strlen($this->_buffer[$fd]) < $this->_headers[$fd]['length'])
        {
            return true;
        }

        //数据解包
        $request = self::decode($this->_buffer[$fd], $this->_headers[$fd]['type']);
        if ($request === false)
        {
            $this->sendErrorMessage($fd, self::ERR_UNPACK);
        }
        //执行远程调用
        else
        {
            //当前请求的头
            self::$requestHeader = $_header = $this->_headers[$fd];
            //调用端环境变量
            if (!empty($request['env']))
            {
                self::$clientEnv = $request['env'];
            }
            //socket信息
            self::$clientEnv['_socket'] = $this->server->connection_info($header['fd']);
            $response = $this->call($request, $_header);
            //发送响应
            $ret = $this->server->send($fd, self::encode($response, $_header['type'], $_header['uid'], $_header['serid']));
            if ($ret === false)
            {
                trigger_error("SendToClient failed. params=".var_export($request, true)."\nheaders=".var_export($_header, true), E_USER_WARNING);
            }
            //退出进程
            if (self::$stop)
            {
                exit(0);
            }
        }
        //清理缓存
        unset($this->_buffer[$fd], $this->_headers[$fd]);
        return true;
    }

    /**
     * 获取客户端环境信息
     * @return array
     */
    static function getClientEnv()
    {
        return self::$clientEnv;
    }

    /**
     * 获取请求头信息，包括UID、Serid串号等
     * @return array
     */
    static function getRequestHeader()
    {
        return self::$requestHeader;
    }

    function sendErrorMessage($fd, $errno)
    {
        return $this->server->send($fd, self::encode(array('errno' => $errno), $this->_headers[$fd]['type']));
    }

    /**
     * 打包数据
     * @param $data
     * @param $type
     * @param $uid
     * @param $serid
     * @return string
     */
    static function encode($data, $type = self::DECODE_PHP, $uid = 0, $serid = 0)
    {
        //启用压缩
        if ($type & self::DECODE_GZIP)
        {
            $_type = $type & ~self::DECODE_GZIP;
            $gzip_compress = true;
        }
        else
        {
            $gzip_compress = false;
            $_type = $type;
        }
        switch($_type)
        {
            case self::DECODE_JSON:
                $body = json_encode($data);
                break;
            case self::DECODE_PHP:
            default:
                $body = serialize($data);
                break;
        }
        if ($gzip_compress)
        {
            $body = gzencode($body);
        }
        return pack(SOAServer::HEADER_PACK, strlen($body), $type, $uid, $serid) . $body;
    }

    /**
     * 解包数据
     * @param string $data
     * @param int $unseralize_type
     * @return string
     */
    static function decode($data, $unseralize_type = self::DECODE_PHP)
    {
        if ($unseralize_type & self::DECODE_GZIP)
        {
            $unseralize_type &= ~self::DECODE_GZIP;
            $data = gzdecode($data);
        }
        switch ($unseralize_type)
        {
            case self::DECODE_JSON:
                return json_decode($data, true);
            case self::DECODE_PHP;
            default:
                return unserialize($data);
        }
    }

    /**
     * @param $serv
     * @param int $fd
     * @param $from_id
     */
    function onClose($serv, $fd, $from_id)
    {
        unset($this->_buffer[$fd]);
    }

    /**
     * 增加命名空间
     * @param $name
     * @param $path
     *
     * @throws \Exception
     */
    function addNameSpace($name, $path)
    {
        if (!is_dir($path))
        {
            throw new \Exception("$path is not real path.");
        }
        Loader::addNameSpace($name, $path);
    }

    /**
     * 调用远程函数
     * @param $request
     * @return array
     */
    protected function call($request, $header)
    {
        if (empty($request['call']))
        {
            return array('errno' => self::ERR_PARAMS);
        }
        //函数不存在
        if (!is_callable($request['call']))
        {
            return array('errno' => self::ERR_NOFUNC);
        }
        $ret = call_user_func_array($request['call'], $request['params']);
        if ($ret === false)
        {
            return array('errno' => self::ERR_CALL);
        }
        return array('errno' => 0, 'data' => $ret);
    }

    /**
     * 关闭连接
     * @param $fd
     */
    protected function close($fd)
    {
        $this->server->close($fd);
        unset($this->_buffer[$fd], $this->_headers[$fd]);
    }
}