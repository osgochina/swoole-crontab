<?php


namespace App\Controller;
use App;
use Swoole;

/**
 * @RBAC 管理正在运行的任务
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-8
 * Time: 下午2:51
 */
class Runtimetask extends App\CommonController
{
    /**
     * @RBAC 显示正在运行的任务
     */
    public function index()
    {
        //页数
        if (!empty($_GET['pagesize'])) {
            $pagesize = intval($_GET['pagesize']);
        } else {
            $pagesize = 20;
        }
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $ret = App\Service::getInstance()->call("Tasks::getRuntimeTasks",$page,$pagesize)->getResult(10);
        $pager = new Swoole\Pager(array('total'=> $ret["total"], 'perpage'  => $pagesize, 'nowindex' => $page));
        $this->assign('pager', array('total' => $ret["total"], 'pagesize' => $pagesize, 'render' => $pager->render()));
        $this->assign("list",$this->formatData($ret["rows"]));
        $this->display();
    }
    private function formatData($data)
    {
        foreach ($data as &$value)
        {
            $value["runStatus_f"] = isset(self::$runStatus[$value["runStatus"]])?self::$runStatus[$value["runStatus"]]:"未知";
        }
        return $data;
    }
}