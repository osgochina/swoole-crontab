<?php
/**
 * worker服务中  新创建一个进程去执行命令
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午6:04
 */
namespace Lib;
use Swoole;
class Process
{
    private static $table;
    static private $column = [
        "taskId" => [\swoole_table::TYPE_INT, 8],
        "runid" => [\swoole_table::TYPE_INT, 8],
        "status" => [\swoole_table::TYPE_INT, 1],
        "start" => [\swoole_table::TYPE_FLOAT, 8],
        "end" => [\swoole_table::TYPE_FLOAT, 8],
        "code"=> [\swoole_table::TYPE_INT, 1],
        "pipe"=> [\swoole_table::TYPE_INT, 8],
    ];
    const PROCESS_START = 0;//程序开始运行
    const PROCESS_STOP = 1;//程序结束运行

    public $task;
    static public $process_list = [];
    private  static $process_stdout = [];
    private static $max_stdout = 10240;

    public static function init()
    {
        self::$table = new \swoole_table(ROBOT_MAX_PROCESS);
        foreach (self::$column as $key => $v) {
            self::$table->column($key, $v[0], $v[1]);
        }
        self::$table->create();
    }

    /**
     * 注册信号
     */
    public static function signal()
    {
        \swoole_process::signal(SIGCHLD, function($sig) {
            //必须为false，非阻塞模式
            while($ret =  \swoole_process::wait(false)) {
                $pid = $ret['pid'];
                if (self::$table->exist($pid)){
                    $task = self::$table->get($pid);
                    $task["status"] = self::PROCESS_STOP;
                    $task["end"] = microtime(true);
                    $task["code"] = $ret["code"];
                    self::$table->set($pid,$task);
                    swoole_event_del($task["pipe"]);
                    unset(self::$process_list[$pid]);
                    self::log($task["runid"],$task["taskId"],"进程运行完成,输出值",isset(self::$process_stdout[$pid])?self::$process_stdout[$pid]:"");
                    unset(self::$process_stdout[$pid]);

                }
            }
        });
    }

    /**
     * 通知中心任务执行结果
     * @return bool
     */
    public static function notify()
    {
        if (count(self::$table) >0){
            $procs= [];
            $client = Client::getInstance();
            foreach (self::$table as $pid=>$process){
                if ($process["status"] == self::PROCESS_STOP){
                    $procs[$pid] = [
                        "taskId"=>$process["taskId"],
                        "runid"=>$process["runid"],
                        "start"=>$process["start"],
                        "end"=>$process["end"],
                        "code"=>$process["code"],
                    ];
                }
            }
            if (empty($procs)) return true;
            foreach ($procs as $pid=>$proc){
                self::log($proc["runid"],$proc["taskId"],"通知中心服",$proc);
            }
            $rect = $client->call("Agent::notify",$procs);
            $ret = $rect->getResult(10);
            if (empty($ret)){
                Flog::log("tasks通知中心服失败,code".$rect->code.",msg".$rect->msg);
                return false;
            }
            foreach ($procs as $pid=>$v){
                self::$table->del($pid);
            }
        }
        return true;
    }

    /**
     * 创建一个子进程
     * @param $task
     * @return bool
     */
    public static function create_process($task)
    {
        $cls = new self();
        $cls->task = $task;
        $process = new \swoole_process(array($cls, "run"),true,true);
        $pid = $process->start();
        if ($pid) {
            swoole_event_add($process->pipe, function($pipe) use ($pid) {
                if (!isset(self::$process_stdout[$pid])) self::$process_stdout[$pid]="";
                $tmp = self::$process_list[$pid]->read();
                $len = strlen(self::$process_stdout[$pid]);
                if ($len+strlen($tmp) <= self::$max_stdout){
                    self::$process_stdout[$pid] .= $tmp;
                }
            });
            self::log($task["runid"],$task["id"],"进程开始执行",$task);
            self::$table->set($pid,["taskId"=>$task["id"],"runid"=>$task["runid"],"status"=>self::PROCESS_START,"start"=>microtime(true),"pipe"=>$process->pipe]);
            self::$process_list[$pid] = $process;
            return true;
        }else{
            self::log($task["runid"],$task["id"],"创建子进程失败",$task);
        }
        return false;
    }

    /**
     * 子进程执行的入口
     * @param $worker
     */
    public function run($worker)
    {
        foreach (self::$process_list as $p){
            unset($p);
        }
        $exec = $this->task["execute"];
        $worker->name($exec ."#". $this->task["id"]);
        $exec = explode(" ",trim($exec));
        $execfile = array_shift($exec);
        if (!self::changeUser($this->task["runuser"])){
            echo "修改运行时用户失败\n";
            exit(101);
        }
        $worker->exec($execfile,$exec);
    }

    /**
     * 修改运行时用户
     * @param $user
     * @return bool
     */
    static function changeUser($user)
    {
        if (!function_exists('posix_getpwnam'))
        {
            trigger_error(__METHOD__ . ": require posix extension.");

            return false;
        }
        $user = posix_getpwnam($user);
        if ($user)
        {
            posix_setuid($user['uid']);
            posix_setgid($user['gid']);
            return true;
        }
        else
        {
            return false;
        }
    }


    static function log($runid,$taskid,$explain,$msg="")
    {
        $log = [
            "taskid"=>$taskid,
            "runid"=>$runid,
            "explain"=>$explain,
            "msg"=>is_scalar($msg) ? $msg : json_encode($msg),
            "createtime"=>date("Y-m-d H:i:s"),
        ];
        Client::getInstance()->call("Termlog::addLogs",[$log])->getResult();
    }
}