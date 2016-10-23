<?php
namespace App\Controller;
use App\RBAC;
use Swoole;

class Page extends Swoole\Controller
{
    /**
     * 登陆
     */
    function index()
    {
        $this->login();
        $this->display();
    }

    public function login()
    {
        $this->session->start();
        if ($this->user->isLogin()){
            $this->http->redirect($this->config["user"]["home_url"]);
            return;
        }
        if (!empty($_POST)){
            if (!empty($_POST['password']))
            {
                $r = $this->user->login(trim($_POST['username']), $_POST['password'],$_POST["auto_login"]);
                if ($r)
                {
                    $this->user->saveUserinfo();
                    $this->user->updateStatus();
                    $_SESSION["rbac_list"] = RBAC::loadAccess($_SESSION['user_id']);

                    $this->http->redirect($this->config["user"]["home_url"]);
                    return;
                }
                else
                {
                    $this->assign("message","用户名或密码错误");
                }
            }
        }
    }

    /**
     * 登出
     */
    function logout()
    {
        $this->session->start();
        $_SESSION = array();
        $this->swoole->http->redirect($this->swoole->config['user']['login_url']);
    }

    public function switch_group()
    {
        $this->session->start();
        $gid = $_GET["gid"];
        $gname = $_GET["gname"];
        $_SESSION["_gid"] = $gid;
        $_SESSION["_gname"] = $gname;
        $this->request->redirect($_GET['refer']);
    }
}
