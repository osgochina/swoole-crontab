<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-21
 * Time: 下午11:14
 */

namespace App\Controller;


use App\CommonController;

class Password extends CommonController
{


    function modifyPassword()
    {
        if (!$this->user->isLogin()){
            $this->http->redirect("/page/index");
            return;
        }
        if (!empty($_POST)){
            $userid = $_SESSION["user_id"];
            $oldpasswd = $_POST["oldpassword"];
            $password = $_POST["password"];
            if (!$this->user->changePassword($userid,$oldpasswd,$password)){
                $this->setMessage(1,"修改失败");
                goto end;
            }
            $this->setMessage(0,"修改成功");
        }
        end:
        $this->display();
    }
}