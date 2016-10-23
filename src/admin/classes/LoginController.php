<?php
namespace App;

class LoginController extends \Swoole\Controller
{
    protected $uid;
    protected $userinfo;

    protected $errCode;
    protected $errMsg;
    function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $swoole->session->start();
        // 控制器方法名
        define('VIEW_NAME', trim(\Swoole::$php->env['mvc']['view']));
        if (!$this->isLogin())
        {
            if (!empty($_SERVER['REQUEST_URI']))
            {
                $this->swoole->http->redirect($this->swoole->config['user']['login_url']."?refer=".base64_encode($_SERVER['REQUEST_URI']));
            }
            else
            {
                $this->swoole->http->redirect($this->swoole->config['user']['login_url']);
            }
            $this->swoole->http->finish();
        }
        else
        {
            $this->uid = $_SESSION['user_id'];
            $this->userinfo = $_SESSION['userinfo'];
            if (!empty($this->userinfo['blocking']))
            {
                $this->http->finish("<h2>您的账户已被禁用！</h2><hr/><p>请联系：管理员</p><br><a href='/page/logout'>重新登陆</a>");
            }
            if (!RBAC::auth(\Swoole::$php->env['mvc']) && $this->uid != 1){
                $this->http->finish("<h2>您的账户没有权限！</h2><hr/><p>请联系：管理员</p><a href='javascript:window.history.back();'>返回上一页</a><br><a href='/page/logout'>重新登陆</a>");
            }
        }
    }
    protected function isLogin()
    {
        if (isset($_SESSION['isLogin']) and $_SESSION['isLogin'] == 1)
        {
            return true;
        }
        return false;
    }

    /**
     * 检查是否允许
     * @param $optype
     * @param $id
     * @return bool
     */
    protected function isAllow($optype, $id = 0)
    {
        if ($this->userinfo['usertype'] == 0)
        {
            return true;
        }
        else
        {
            if (empty($this->userinfo['rules']))
            {
                return false;
            }
            else
            {
                return strstr($this->userinfo['rules'], $optype) !== false;
            }
        }
    }

    protected function isActiveMenu($m, $v = '')
    {
        if ($this->env['mvc']['controller'] == $m)
        {
            if (!empty($v))
            {
                return $this->env['mvc']['view'] == $v;
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    protected function isShowMenu($m,$v)
    {
        return RBAC::auth(["controller"=>$m,"view"=>$v]);
    }

    protected function validate(array $data, callable $callback, &$errors)
    {
        return call_user_func_array($callback, [$data, &$errors]);
    }

    protected function redirect($url)
    {
        return $this->http->header('Location', $url);
    }

    protected function success($msg, $url)
    {
        return \Swoole\JS::js_goto($msg, $url);
    }

    protected function error($msg)
    {
        $this->assign('msg', $msg);
        $this->display('common/error.php');
    }
}
