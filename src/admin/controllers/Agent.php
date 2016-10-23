<?php


namespace App\Controller;
use App;
use Swoole;

/**
 * @RBAC 管理代理服务器
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-8
 * Time: 下午2:51
 */
class Agent extends App\CommonController
{
    /**
     * @RBAC 显示代理服务器
     */
    public function index()
    {
        //页数
        if (!empty($_GET['pagesize'])) {
            $pagesize = intval($_GET['pagesize']);
        } else {
            $pagesize = 20;
        }
        $gets = [];
        if (!empty($_GET["gid"])){
            $gets["gid"] = trim($_GET["gid"]);
        }
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $ret = App\Service::getInstance()->call("Agent::getAgents",$gets,$page,$pagesize)->getResult(10);
        $pager = new Swoole\Pager(array('total'=> $ret["total"], 'perpage'  => $pagesize, 'nowindex' => $page));
        $this->assign('pager', array('total' => $ret["total"], 'pagesize' => $pagesize, 'render' => $pager->render()));
        $this->assign("list",$this->formatData($ret["rows"]));
        $group = App\Service::getInstance()->call("Tasks::getGroups",$_SESSION["user_id"])->getResult(10);
        $this->assign("group",$group);
        $this->display();
    }

    private function formatData($data)
    {
        foreach ($data as &$value)
        {
            $value["status_f"] = $value["status"] == 1?"暂停":"正常";
            $value["isregister_f"] = $value["isregister"] == 1?"已注册":"未注册";
        }
        return $data;
    }

    /**
     *  @RBAC 添加编辑代理
     * addOrEdit
     */
    public function addOrEdit()
    {
        $default = [];
        if (isset($_GET["id"])){
            $id = $_GET["id"];
            $ret = App\Service::getInstance()->call("Agent::getAgent",$id)->getResult(10);
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

                    $default = $data["gids"];
                }
            }
        }

        if (!empty($_POST)){
            $task = [];
            if (isset($_POST["gids"])){
                $task["gids"] = $_POST["gids"];
                unset($task["gids"]["-1"]);//去除全部这个选项
            }
            if (isset($_POST["alias"])){
                $task["alias"] =trim($_POST["alias"]);
            }
            if (isset($_POST["ip"])){
                $task["ip"] = trim($_POST["ip"]);
            }
            if (isset($_POST["status"])){
                $task["status"] = trim($_POST["status"]);
            }
            if ($_POST["id"]){
                $ret = App\Service::getInstance()->call("Agent::updateAgent",$_POST["id"],$task)->getResult(10);
            }else{
                $task["port"] = 8902;
                $ret = App\Service::getInstance()->call("Agent::addAgent",$task)->getResult(10);
            }
            if (empty($ret)){
                $this->setMessage(1,"没有响应");
            }else{
                if (isset($ret["data"])) $_POST["id"] = $ret["data"];
                $this->setMessage($ret["code"],$ret["msg"]);
            }
            foreach ($_POST as $key=>$value){
                $this->tpl_var[$key] = $value;
            }
            $default = $_POST["gids"];

        }

        $group = App\Service::getInstance()->call("Tasks::getGroups",$_SESSION["user_id"])->getResult(10);
        $group["-1"] = "全部";
        $this->assign('gids', \Swoole\Form::muti_select('gids[]', $group, $default, null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:200%"), false));
        $this->display();
    }
    /**
     * @RBAC 删除代理
     * @return array|string
     */
    public function delete()
    {
        if ($_POST["cid"]){
            $ret = App\Service::getInstance()->call("Agent::deleteAgent",$_POST["cid"])->getResult(10);
            if (empty($ret)){
                return $this->message(1,"没有响应");
            }else{
                return $this->message($ret["code"],$ret["msg"]);
            }
        }
    }
}