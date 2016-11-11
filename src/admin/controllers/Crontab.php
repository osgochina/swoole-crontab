<?php


namespace App\Controller;

use App;
use Swoole;
/**
 * Created by PhpStorm.
 *  @RBAC 定时任务管理
 * User: liuzhiming
 * Date: 16-9-2
 * Time: 上午11:41
 */
class Crontab extends App\CommonController
{

    /**
     * @RBAC 定时任务列表
     * @throws \Exception
     */
    public function index()
    {
        $gets = [];
        //页数
        if (!empty($_GET['pagesize'])) {
            $pagesize = intval($_GET['pagesize']);
        } else {
            $pagesize = 10;
        }
        $gets["gid"] = $_SESSION["_gid"];
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $ret = App\Service::getInstance()->call("Tasks::getList",$gets,$page,$pagesize)->getResult(10);
        $pager = new Swoole\Pager(array('total'=> $ret["total"], 'perpage'  => $pagesize, 'nowindex' => $page));
        $this->assign('pager', array('total' => $ret["total"], 'pagesize' => $pagesize, 'render' => $pager->render()));
        $this->assign("list",$this->formatData($ret["rows"]));
        $this->display();
    }



    private function formatData($data)
    {
        foreach ($data as &$value)
        {
            $value["runTimeStart"] = !empty($value["runTimeStart"])?date("Y-m-d H:i:s",$value["runTimeStart"]):"";
            $value["runUpdateTime"] = !empty($value["runUpdateTime"])?date("Y-m-d H:i:s",$value["runUpdateTime"]):"";
            $value["status_f"] = $value["status"] == 1?"暂停":"正常";
            $value["runStatus_f"] = isset(self::$runStatus[$value["runStatus"]])?self::$runStatus[$value["runStatus"]]:"未知";
        }
        return $data;
    }

    /**
     *  @RBAC 添加编辑定时任务
     */
    public function addOrEdit()
    {
        $manager = [$_SESSION["userinfo"]["username"]];
        $this->assign("gname",$_SESSION["_gname"]);
        if (isset($_GET["id"])){
            $id = $_GET["id"];
            $ret = App\Service::getInstance()->call("Tasks::get",$id)->getResult(10);
            if (empty($ret)){
                $this->setMessage(1,"没有响应");
            }else{
                if ($ret["code"]){
                    $this->setMessage($ret["code"],$ret["msg"]);
                }else{
                    $data = $ret["data"];
                    foreach ($data as $key=>$value){
                        $this->tpl_var[$key] = $value;
                    }
                    $manager = isset($data["manager"])?explode(",",$data["manager"]):[];
                    $agentids = isset($data["agents"])?explode(",",$data["agents"]):[];
                    $agentids = array_flip($agentids);
                }
            }
        }
        if (!empty($_POST)){
            $task = [];
            if (isset($_POST["taskname"])){
                $task["taskname"] = trim($_POST["taskname"]);
            }
            if (isset($_POST["rule"])){
                $task["rule"] =trim($_POST["rule"]);
            }
            if (isset($_POST["runnumber"])){
                $task["runnumber"] = trim($_POST["runnumber"]);
            }
            if (isset($_POST["execute"])){
                $task["execute"] = htmlspecialchars_decode(trim($_POST["execute"]));
            }
            if (isset($_POST["status"])){
                $task["status"] = trim($_POST["status"]);
            }
            if (isset($_POST["runuser"])){
                $task["runuser"] = trim($_POST["runuser"]);
            }
            if (isset($_POST["manager"])){
                $task["manager"] = implode(",",$_POST["manager"]);
            }
            if (isset($_POST["agents"])){
                $task["agents"] = implode(",",$_POST["agents"]);
            }
            if ($_POST["id"]){
                $ret = App\Service::getInstance()->call("Tasks::update",$_POST["id"],$task)->getResult(10);
            }else{
                $task["gid"] = $_SESSION["_gid"];
                $ret = App\Service::getInstance()->call("Tasks::add",$task)->getResult(10);
            }
            if ($this->is_ajax()){
                if (empty($ret)){
                    return $this->message(1,"没有响应");
                }else{
                    return $this->message($ret["code"],$ret["msg"]);
                }
            }else{
                if (empty($ret)){
                    $this->setMessage(1,"没有响应");
                }else{
                    if (isset($ret["data"][0])) $_POST["id"] = $ret["data"][0];
                    $this->setMessage($ret["code"],$ret["msg"]);
                }
                foreach ($_POST as $key=>$value){
                    $this->tpl_var[$key] = $value;
                }
            }
            $manager = isset($_POST["manager"])?$_POST["manager"]:[];
            $agentids = isset($_POST["agents"])?$_POST["agents"]:[];
            $agentids = array_flip($agentids);
        }
        $group = App\Service::getInstance()->call("Tasks::getGroups")->getResult(10);
        $this->assign("group",$group);
        $userlist = model('User')->gets(["order"=>"username asc"]);
        $user_uids = [];
        foreach ($userlist as $v){
            $user_uids[$v["username"]] = $v["nickname"]."(".$v["username"].")";
        }
        $this->assign('manager', \Swoole\Form::muti_select('manager[]', $user_uids, $manager, null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false));

        $agents = App\Service::getInstance()->call("Agent::getAgentByGid",$_SESSION["_gid"])->getResult(10);
        if (!empty($agents)){
            foreach ($agents as &$value)
            {
                if (isset($agentids[$value["id"]])){
                    $value["checked"] = true;
                }
            }
        }
        $this->assign("agents",$agents);
        $this->display();
    }

    /**
     * @RBAC 删除定时任务
     * @return array|string
     */
    public function delete()
    {
        if ($_POST["id"]){
            $ret = App\Service::getInstance()->call("Tasks::delete",$_POST["id"])->getResult(10);
            if (empty($ret)){
                return $this->message(1,"没有响应");
            }else{
                return $this->message($ret["code"],$ret["msg"]);
            }
        }
    }
}