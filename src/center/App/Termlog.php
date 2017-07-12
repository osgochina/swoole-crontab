<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-18
 * Time: 下午2:22
 */

namespace App;

use Lib;

class Termlog
{


    /**
     * 添加日志
     * @param $logs
     * @return array
     */
    public static function addLogs($logs)
    {
        if (!is_array($logs) || count($logs) < 1) {
            return Lib\Util::errCodeMsg(101, "参数有误");
        }
        foreach ($logs as $log) {
            $tmp = [
                "taskid" => $log["taskid"],
                "runid" => $log["runid"],
                "explain" => $log["explain"],
                "msg" => is_scalar($log["msg"]) ? $log["msg"] : json_encode($log["msg"]),
                "createtime" => $log["createtime"],
            ];
            Lib\TermLog::getInstance()->put($tmp);
        }
        return Lib\Util::errCodeMsg(0, "保存成功");
    }

    /**
     * 获取日志列表
     * @param array $gets
     * @param int $page
     * @param int $pagesize
     * @return array
     */
    public static function getLogs($gets = [], $page = 1, $pagesize = 10)
    {
        //页数
        if (!empty($pagesize)) {
            $gets['pagesize'] = intval($pagesize);
        } else {
            $gets['pagesize'] = 20;
        }
        $gets['page'] = !empty($page) ? $page : 1;
        $gets["order"] = "runid DESC,createtime ASC";
        $pager = "";
        $db = table("term_logs");
        $list = $db->gets($gets, $pager);
        $tasks = Lib\LoadTasks::getTasks();
        foreach ($list as &$value) {
            $tmp = $tasks->get($value["taskid"]);
            $value["taskname"] = $tmp["taskname"];
        }
        return ["total" => $pager->total, "rows" => $list];
    }

    /**
     * 清除一个月前的日志
     * @return array
     */
    public static function cleanLogs()
    {
        $datetime = date("Y-m-d", strtotime(date("Y-m-d") . "-1 month"));
        $db = table("term_logs");
        if (!$db->dels(["where" => ["createtime<'" . $datetime . "'"]])) {
            return Lib\Util::errCodeMsg(1, "删除失败");
        }
        return Lib\Util::errCodeMsg(0, "删除成功");
    }

}