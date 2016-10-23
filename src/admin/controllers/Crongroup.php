<?php


namespace App\Controller;
use App;

/**
 * @RBAC 定时任务分组
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-8
 * Time: 上午11:13
 */
class Crongroup extends App\CommonController
{


    /**
     * @RBAC 显示分组列表
     * index
     */
    public function index()
    {
        $ret = App\Service::getInstance()->call("Tasks::getGroups")->getResult(10);
        $this->assign("list",$ret);
        $this->display();
    }
    /**
     *  @RBAC 添加编辑分组
     * addOrEdit
     */
    public function addOrEdit()
    {
        $uids =[];
        if (isset($_GET["gid"])){
            $gid = $_GET["gid"];
            $ret = App\Service::getInstance()->call("Tasks::getGroup",$gid)->getResult(10);
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
                    $uids = isset($data["uids"])?$data["uids"]:[];

                }
            }
        }
        if (!empty($_POST)){
            $group = [];
            if (isset($_POST["gname"])){
                $group["gname"] =trim($_POST["gname"]);
            }
            if (isset($_POST["uids"])){
                $group["uids"] = $_POST["uids"];
            }
            if ($_POST["gid"]){
                $ret = App\Service::getInstance()->call("Tasks::updateGroup",$_POST["gid"],$group)->getResult(10);
            }else{
                $ret = App\Service::getInstance()->call("Tasks::addGroup",$group)->getResult(10);
            }
            if (empty($ret)){
                $this->setMessage(1,"没有响应");
            }else{
                if (isset($ret["data"])) $_POST["gid"] = $ret["data"];
                $this->setMessage($ret["code"],$ret["msg"]);
            }
            foreach ($_POST as $key=>$value){
                $this->tpl_var[$key] = $value;
            }
            $uids = isset($_POST["uids"])?$_POST["uids"]:[];
        }
        $userlist = model('User')->gets(["order"=>"username asc"]);
        $user_uids = [];
        foreach ($userlist as $v){
            $user_uids[$v["id"]] = $v["nickname"]."(".$v["username"].")";
        }
        $this->assign('uids', \Swoole\Form::muti_select('uids[]', $user_uids, $uids, null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false));
        $this->display();
    }

    /**
     *  @RBAC 删除分组
     * @return array|string
     */
    public function delete()
    {
        if ($_POST["gid"]){
            $ret = App\Service::getInstance()->call("Tasks::deleteGroup",$_POST["gid"])->getResult(10);
            if (empty($ret)){
                return $this->message(1,"没有响应");
            }else{
                return $this->message($ret["code"],$ret["msg"]);
            }
        }
    }
}