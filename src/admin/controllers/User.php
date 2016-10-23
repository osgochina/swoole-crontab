<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-18
 * Time: 下午10:19
 */

namespace App\Controller;


use App;

/**
 * @RBAC 用户管理
 * Class User
 * @package App\Controller
 */
class User extends App\CommonController
{

    /**
     * @RBAC 显示列表
     * 显示列表
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
            $gets['order'] = 'id desc';
        }
        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $pager="";
        $list =  model("User")->gets($gets, $pager);
        $this->assign('pager', array('total' => $pager->total, 'pagesize' => $gets['pagesize'], 'render' => $pager->render()));
        $this->assign("list",$list);
        $this->display();
    }

    /**
     * @RBAC 添加修改用户
     * 添加修改用户
     */
    public function addOrEdit()
    {
        $user = model("User");
        if (!empty($_POST)){
            $userinfo = [];
            if (isset($_POST["username"])){
                $userinfo["username"] = trim($_POST["username"]);
                if ($user->get($userinfo["username"],"username")->exist()){
                    $this->setMessage(102,"用户名已存在");
                    foreach ($_POST as $k=>$v)
                        $this->tpl_var[$k] =$v;
                    goto end;
                }
            }
            if (isset($_POST["password"])){
                $userinfo["password"] = App\Auth::makePasswordHash($userinfo["username"],trim($_POST["password"]));
            }
            if (isset($_POST["nickname"])){
                $userinfo["nickname"] = trim($_POST["nickname"]);
            }
            if (isset($_POST["blocking"])){
                $userinfo["blocking"] = trim($_POST["blocking"]);
            }

            if (empty($_POST["id"])){
                $userinfo["createtime"] = date("Y-m-d H:i:s");
                if (!($id = $user->put($userinfo))){
                    $this->setMessage(102,"添加失败");
                    goto end;
                }
                $this->setMessage(0,"添加成功");
            }else{
                if (!$user->set($_POST["id"],$userinfo)){
                    $this->setMessage(102,"修改失败");
                    goto end;
                }
                $this->setMessage(0,"修改成功");
                $id = $_POST["id"];
            }
        }
        if (isset($_GET["id"])){
            $id = trim($_GET["id"]);
        }
        if (empty($id)) goto end;
        $userinfo = $user->get($id);
        if (!$userinfo->exist()){
            $this->setMessage(1,"用户不存在");
            goto end;
        }
        $this->tpl_var["id"] = $userinfo["id"];
        $this->tpl_var["username"] = $userinfo["username"];
        $this->tpl_var["nickname"] = $userinfo["nickname"];
        $this->tpl_var["blocking"] = $userinfo["blocking"];

        end:
        $this->display();
    }

    /**
     * @RBAC 删除用户
     * 删除用户
     */
    public function delete()
    {
        if (isset($_POST["id"])){
            $id = trim($_POST["id"]);
            if ($id == 1){
                return $this->message(1,"不能删除管理员");
            }
            $user = model("User");
            if (!$user->del($id)){
                return $this->message(1,"删除用户失败");
            }
            $table = table("rbac_user_group");
            if (!$table->dels(["userid"=>$id])){
                return $this->message(1,"删除用户分组关系失败");
            }
            return $this->message(0,"删除成功");
        }
    }

    /**
     * @RBAC 修改密码
     * 修改密码
     */
    public function modifypassword()
    {
        $userid = $_REQUEST["id"];
        if ($userid){
            $user = model("User");
            $userinfo = $user->get($userid);
            if (!$userinfo->exist()){
                $this->setMessage(1,"用户不存在");
                goto end;
            }
            if (!empty($_POST)){
                $username = $_POST["username"];
                $password = $_POST["password"];
                $ret = $user->set($userid,["password"=>App\Auth::makePasswordHash($username,$password)]);
                if (!$ret){
                    $this->setMessage(1,"修改失败");
                    goto end;
                }
                $this->setMessage(0,"修改成功");
            }
            $this->tpl_var["id"] = $userinfo["id"];
            $this->tpl_var["username"] = $userinfo["username"];
        }
        end:
        $this->display();
    }

}