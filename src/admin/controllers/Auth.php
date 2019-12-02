<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-7-18
 * Time: 上午11:45
 */

namespace App\Controller;

use App;

/**
 * Class Auth
 * @RBAC 权限管理系统
 * @package App\Controller
 */
class Auth extends App\CommonController
{
    /**
     * @RBAC 分组列表
     * @throws \Exception
     */
    public function index()
    {
        //页数
        if (!empty($_GET['pagesize']))
        {
            $gets['pagesize'] = intval($_GET['pagesize']);
        }
        else
        {
            $gets['pagesize'] = 20;
        }
        //排序
        if (!empty($_GET['order']))
        {
            $gets['order'] = str_replace('@', ' ', $_GET['order']);
        }
        else
        {
            $gets['order'] = 'gid desc';
        }
        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $pager="";
        $list =  table("rbac_group")->gets($gets, $pager);
        $this->assign('pager', array('total' => $pager->total, 'pagesize' => $gets['pagesize'], 'render' => $pager->render()));
        $this->assign("list",$list);
        $this->display();
    }

    /**
     *
     * @RBAC 编辑分组
     */
    public function groupedit()
    {
        $default = [];
        $mod = table("rbac_group");
        $mod->primary = "gid";
        $table = table("rbac_user_group");
        $table->primary = "idx";
        if (isset($_GET["gid"])){
            $gid = $_GET["gid"];
            $data = $mod->get($gid,"gid");
            if(!$data->exist()){
                $this->setMessage(1,"不存在gid".$gid);
            }else{
                $this->tpl_var["gid"] = $data["gid"];
                $this->tpl_var["gname"] = $data["gname"];
                $this->tpl_var["status"] = $data["status"];
            }
        }
        if (!empty($_POST)){
            $gid = isset($_POST["gid"])?$_POST["gid"]:"";
            $gname= $_POST["gname"];
            $status = $_POST["status"];
            $userids = $_POST["user_uids"];
            $info = $mod->get($gname,"gname");
            if (empty($gid)){
                if ($info->exist()){
                    $this->setMessage(1,"已存在此名称的分组");
                }else{
                    self::beginTransaction();
                    $data =[
                        "gname"=>$gname,
                        "status"=>$status,
                        "lastupdate"=> date("Y-m-d H:i:s"),
                    ];
                    $gid = $mod->put($data);
                    $table->dels(["gid"=>$gid]);
                    foreach ($userids as $u){
                        $table->put(["gid"=>$gid,"userid"=>$u]);
                    }
                    self::commit();
                    $this->setMessage(0,"添加成功");
                }

            }else{
                if ($info->exist() && $info["gid"] != $gid){
                    $this->setMessage(1,"已存在此名称的分组");
                }else{
                    $data =[
                        "gid"=>$gid,
                        "gname"=>$gname,
                        "status"=>$status,
                        "lastupdate"=> date("Y-m-d H:i:s"),
                    ];
                    self::beginTransaction();
                    $table->dels(["gid"=>$gid]);
                    foreach ($userids as $u){
                        $table->put(["gid"=>$gid,"userid"=>$u]);
                    }
                    if ($mod->set($gid,$data)){
                        self::commit();
                        $this->setMessage(0,"操作成功");
                    }else{
                        self::rollback();
                        $this->setMessage(1,"操作失败");
                    }
                }

            }
            foreach ($_POST as $key=>$value){
                $this->tpl_var[$key] = $value;
            }
        }
        $userlist = model('User')->gets(["order"=>"username asc"]);
        $user_uids = [];
        foreach ($userlist as $v){
            $user_uids[$v["id"]] = $v["nickname"]."(".$v["username"].")";
        }

        if (!empty($gid)){
            $usrs = $table->gets(["gid"=>$gid]);
            foreach ($usrs as $u){
                $default[] = $u["userid"];
            }
        }
        $this->assign('user_uids', \Swoole\Form::muti_select('user_uids[]', $user_uids, $default, null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false));
        $this->display();
    }


    /**
     * @RBAC 节点权限
     * @throws \Exception
     */
    public function nodeedit()
    {
        $gid = $_REQUEST["gid"];
        $list = [];
        if (!empty($gid)){
            $table = table("rbac_node");
            $table->primary = "idx";
            if (!empty($_POST)){
                $ret = $this->formatNodeList();
                self::beginTransaction();
                if ($table->dels(array("gid"=>$gid))){
                    foreach ($ret as $d){
                        $table->put($d);
                    }
                    self::commit();
                    $this->setMessage(0,"操作成功");
                }
            }
            $list = App\RBAC::getPublicActionList();
            $data = $table->gets(array("gid"=>$gid));
            $nodes = [];
            foreach ($data as $k=>$val){
                $nodes[$val["node"]] = true;
            }
            foreach ($list as $key=>&$value){
                if (isset($nodes[$key])){
                    $value["checked"] = true;
                    continue;
                }
                foreach ($value["methods"] as $k=>&$v){
                    if (isset($nodes[$key."::".$k])){
                        $v["checked"] = true;
                    }
                }
            }
        }
        $this->assign("gid",$gid);
        $this->assign("list",$list);
        $this->display();
    }

    private function formatNodeList()
    {
        $data = $_POST;
        $gid = $data["gid"];
        unset($data["gid"]);
        $all = [];
        foreach ($data as $key=>$value){
            if ($key == "gid") continue;
            if (stripos($key,"::") === false){
                $all[$key] = "on";
            }
        }
        foreach ($data as $key=>$value){
            foreach ($all as $k=>$v){
                if (stripos($key,$k) !== false){
                    unset($data[$key]);
                }
            }
        }
        $list = array_merge($data,$all);
        $ret = [];
        foreach ($list as $p=>$o){
            $ret[] = ["gid"=>$gid,"node"=>str_replace("\\\\","\\",$p)];
        }
        return $ret;
    }

}