<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午3:13
 */

namespace App;

use Lib;
use Swoole\Protocol\SOAServer;

class Agent
{


    /**
     * worker回调中心服 任务执行状态
     * @param $tasks
     * @return array
     */
    public static function notify($tasks)
    {
        if (empty($tasks) || count($tasks) <= 0) {
            return Lib\Util::errCodeMsg(101, "The tasks can't be empty");
        }
        $header = Lib\LoadTasks::getTasks();
        foreach ($tasks as $task) {
            if ($task["code"] == 0) {
                $runStatus = Lib\LoadTasks::RunStatusSuccess;
            } else {
                $runStatus = Lib\LoadTasks::RunStatusFailed;
                Lib\Report::taskFailed($task["taskId"], $task["runid"], $task["code"]);
            }
            $header->set($task["taskId"], ["runStatus" => $runStatus, "runUpdateTime" => time()]);
            $header->decr($task["taskId"], 'execNum');//减少当前执行数量
            if (Lib\Tasks::$table->exist($task["runid"])) {
                Lib\Tasks::$table->set($task["runid"], ["runStatus" => $runStatus]);
            }
            Lib\TermLog::log($task["runid"], $task["taskId"], "任务已经执行完成", $task);
        }
        return Lib\Util::errCodeMsg(0, "ok");
    }

    /**
     * 获取在线worker
     * @param int $page
     * @param int $size
     * @return array
     */
    public static function getRobots($page = 1, $size = 10)
    {
        $start = ($page - 1) * $size;
        $end = $start + $size;
        $data = [];
        $list = Lib\Robot::$table;
        $n = 0;
        foreach ($list as $id => $rb) {
            $n++;
            if ($n <= $start) {
                continue;
            }
            if ($n > $end) {
                break;
            }
            $data[$id] = $rb;
        }
        return ["total" => count($list), "rows" => $data];
    }

    /**
     * 获取代理服务器
     * @return array
     */
    public static function getAgents($gets = [], $page = 1, $pagesize = 10)
    {
        //页数
        if (!empty($pagesize)) {
            $gets['pagesize'] = intval($pagesize);
        } else {
            $gets['pagesize'] = 20;
        }
        $agg = table("agent_group");
        if (isset($gets["gid"])) {
            $glist = $agg->gets(["gid" => $gets["gid"]]);
            if (!empty($glist)) {
                foreach ($glist as $g) {
                    $tmp[] = $g["aid"];
                }
                $gets["in"] = ["id", $tmp];
            }
            unset($gets["gid"]);
        }
        $gets['page'] = !empty($page) ? $page : 1;
        $pager = "";
        $list = table("agents")->gets($gets, $pager);

        $groups = Tasks::getGroups();

        foreach ($list as $k => $task) {
            $gids = $agg->gets(["aid" => $task["id"]]);
            $tmp = Lib\Robot::$table->get($task["ip"]);
            if (!empty($tmp)) {
                $list[$k]["lasttime"] = $tmp["lasttime"];
                $list[$k]["isregister"] = 1;
            } else {
                $list[$k]["isregister"] = 0;
            }
            $list[$k]["gname"] = "全部";
            if (!empty($gids)) {
                $gname = "";
                foreach ($gids as $val) {
                    if ($val == "-1") {
                        $gname = "全部";
                    } else {
                        $gname = isset($groups[$val['gid']]) ? $groups[$val['gid']] : "未知";
                    }
                }
                $list[$k]["gname"] = $gname;
            }
        }
        return ["total" => $pager->total, "rows" => $list];
    }

    /**
     * 获取单个任务
     * @param $id
     * @return array
     */
    public static function getAgent($id)
    {
        $agent = table("agents")->get($id);
        if (!$agent->exist()) {
            return Lib\Util::errCodeMsg(101, "不存在");
        }
        $gids = table("agent_group")->gets(["aid" => $id]);
        $data = [
            "id" => $agent["id"],
            "alias" => $agent["alias"],
            "ip" => $agent["ip"],
            "status" => $agent["status"],
            "gids" => ["-1"],
        ];
        if (!empty($gids)) {
            $gname = [];
            foreach ($gids as $gid) {
                $gname[$gid["gid"]] = $gid["gid"];
            }
            $data["gids"] = $gname;
        }
        return Lib\Util::errCodeMsg(0, "", $data);
    }

    /**
     * 根据分组id获取agent列表
     * @param $gid
     * @return array
     * @throws \Exception
     */
    public static function getAgentByGid($gid)
    {
        $agg = table("agent_group");
        $glist = $agg->gets(["gid" => $gid]);
        if (empty($glist)) {
            return [];
        }
        foreach ($glist as $g) {
            $aids[] = $g["aid"];
        }
        if (empty($aids)) {
            return [];
        }
        $gets["in"] = ["id", $aids];
        $list = table("agents")->gets($gets);
        $data = [];
        foreach ($list as $value) {
            $data[] = [
                "id" => $value["id"],
                "alias" => $value["alias"],
                "ip" => $value["ip"],
            ];
        }
        return $data;
    }

    /**
     * 添加任务
     * @param $agent
     * @return array
     */
    public static function addAgent($agent)
    {
        if (empty($agent)) {
            return Lib\Util::errCodeMsg(101, "参数为空");
        }
        $gids = $agent["gids"];
        unset($agent["gids"]);
        $id = table("agents")->put($agent);
        if ($id === false) {
            return Lib\Util::errCodeMsg(102, "添加失败");
        }
        $agent_group = table("agent_group");
        foreach ($gids as $gid) {
            $agent_group->put(["gid" => $gid, "aid" => $id]);
        }
        //重新加载代理
        Lib\Robot::$aTable->set($id, ["ip" => $agent["ip"]]);
        return Lib\Util::errCodeMsg(0, "保存成功", $id);
    }

    /**
     *  修改任务
     * @param $id
     * @param $agent
     * @return array
     */
    public static function updateAgent($id, $agent)
    {
        if (empty($id) || empty($agent)) {
            return Lib\Util::errCodeMsg(101, "参数为空");
        }
        $gids = $agent["gids"];
        unset($agent["gids"]);
        if (!table("agents")->set($id, $agent)) {
            return Lib\Util::errCodeMsg(102, "更新失败");
        }
        $agent_group = table("agent_group");
        $agent_group->dels(["aid" => $id]);
        //var_dump($id);
        foreach ($gids as $gid) {
            if ($gid <= 0) {
                continue;
            }
            $agent_group->put(["gid" => $gid, "aid" => $id]);
        }
        self::reload($id);
        return Lib\Util::errCodeMsg(0, "更新成功");
    }

    /**
     * 删除任务代理
     * @param $id
     * @return array
     */
    public static function deleteAgent($id)
    {
        if (empty($id)) {
            return Lib\Util::errCodeMsg(101, "参数为空");
        }
        if (!table("agents")->del($id)) {
            return Lib\Util::errCodeMsg(102, "删除失败");
        }
        table("agent_group")->dels(["aid" => $id]);
        self::reload($id);
        return Lib\Util::errCodeMsg(0, "删除成功");
    }


    private static function reload($aid)
    {
        $agents = table("agents");
        $info = $agents->get($aid);
        if (empty($info) && $info["status"] == 1) {
            Lib\Robot::$aTable->del($aid);
        }
    }


}