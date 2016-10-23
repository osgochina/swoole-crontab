<?php
/**
 * 任务生命周期日志记录
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-31
 * Time: 下午4:18
 */

namespace Lib;


class TermLog
{
    static $handle;
    protected $db;
    protected $table;

    function __construct()
    {
        $this->table = "term_logs";
        $this->db = \Swoole::$php->db('master');
    }

    function put($log)
    {
        \Swoole::$php->db->insert($log, $this->table);
    }

    public static function getInstance()
    {
        if (empty($handle)){
            self::$handle = new self();
        }
        return self::$handle;
    }

    public static function log($runid,$taskid,$explain,$msg="")
    {
        $log = [
            "taskid"=>$taskid,
            "runid"=>$runid,
            "explain"=>$explain,
            "msg"=>is_scalar($msg) ? $msg : json_encode($msg),
            "createtime"=>date("Y-m-d H:i:s"),
        ];
        if (DEBUG == "on"){
            echo $log["createtime"]."\t".$log["runid"]."\t".$log["taskid"]."\t".$log["explain"]."\t".$log["msg"],"\n";
        }
        self::getInstance()->put($log);
    }
}