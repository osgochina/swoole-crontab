<?php
namespace App;
use Swoole;
use App;
class CommonController extends LoginController
{
    
    const RunStatusNormal = 0;//未运行
    const RunStatusStart = 1;//准备运行
    const RunStatusToTaskSuccess = 2;//发送任务成功
    const RunStatusToTaskFailed = 3;//发送任务失败
    const RunStatusSuccess = 4;//运行成功
    const RunStatusFailed = 5;//运行失败

    static $runStatus = [
        self::RunStatusNormal => "未运行",
        self::RunStatusStart => "准备运行",
        self::RunStatusToTaskSuccess => "发送任务成功",
        self::RunStatusToTaskFailed => "发送任务失败",
        self::RunStatusSuccess => "运行成功",
        self::RunStatusFailed => "运行失败",
    ];

    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $group = App\Service::getInstance()->call("Tasks::getGroups",$_SESSION["user_id"])->getResult(10);
        $this->assign("_group",$group);
        if (!isset($_SESSION["_gid"])){
            foreach ($group as $k=>$v){
                $_SESSION["_gid"] = $k;
                $_SESSION["_gname"] = $v;
                break;
            }
        }
        $this->assign("_gid", $_SESSION["_gid"]);
        $this->assign("_gname",$_SESSION["_gname"]);
    }

    protected function is_ajax()
    {
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
           $this->is_ajax = true;
            return true;
        }
        return false;
    }
    

    protected function echoJson($data)
    {
        if (!empty($_REQUEST['jsonp']))
        {
            $this->http->header('Content-type', 'application/x-javascript');
            return $_REQUEST['jsonp'] . "(" . json_encode($data) . ");";
        }
        else
        {
            $this->http->header('Content-type', 'application/json');
            return json_encode($data);
        }
    }
    protected function setMessage($code,$message)
    {
        $this->assign("msg",array("code"=>$code,"message"=>$message));
    }

    public static function beginTransaction()
    {
        return \Swoole::$php->db->start();
    }
    public static function rollback()
    {
        return \Swoole::$php->db->rollback();
    }

    public static function commit()
    {
        return \Swoole::$php->db->commit();
    }
}