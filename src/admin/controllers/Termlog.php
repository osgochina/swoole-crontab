<?php
namespace App\Controller;
use App;
use Swoole;
/**
 * Created by PhpStorm.
 * @RBAC 运行日志管理
 * User: liuzhiming
 * Date: 16-9-18
 * Time: 上午11:54
 */
class Termlog extends App\CommonController
{

    /**
     * @RBAC 日志列表
     * 日志列表
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
        if (isset($_GET["taskid"]) && !empty($_GET["taskid"])){
            $gets["taskid"] = trim($_GET["taskid"]);
        }
        if (isset($_GET["runid"]) && !empty($_GET["runid"])){
            $gets["runid"] = trim($_GET["runid"]);
        }
        // 查询的开始、结束时间
        $begin_date = isset($_GET["begin_date"])?$_GET["begin_date"]:"";
        if (!empty($begin_date))
            $gets['where'][] = "createtime >= '" . $begin_date."'";
        $end_date = isset($_GET["end_date"])?$_GET["end_date"]:"";
        if (!empty($end_date)){
            $end_date = date("Y-m-d",strtotime($end_date) + 86400);// 至当天结束
            $gets['where'][] = "createtime < '" . $end_date."'";
        }
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $ret = App\Service::getInstance()->call("Termlog::getLogs",$gets,$page,$pagesize)->getResult(30);
        $pager = new Swoole\Pager(array('total'=> $ret["total"], 'perpage'  => $pagesize, 'nowindex' => $page));
        $this->assign('pager', array('total' => $ret["total"], 'pagesize' => $pagesize, 'render' => $pager->render()));
        if (!empty($ret["rows"])){
            $this->assign("list",$this->formatData($ret["rows"]));
        }else{
            $this->assign("list",[]);
        }
        $this->display();
    }

    private function formatData($data)
    {
        foreach ($data as &$value)
        {
            if (!empty($value["msg"])){
                $value["msg"] = $this->dump($value["msg"],false);
            }
        }
        return $data;
    }

    function dump($var, $echo=true) {
        $tmp = json_decode($var,true);
        if ($tmp == NULL){
            $tmp = $var;
        }
        ob_start();
        print_r($tmp);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }
}