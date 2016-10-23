<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午2:27
 */

namespace App;

use Lib\Donkeyid;
use Lib\LoadTasks;
use Lib\Util;

class Tasks
{

    /**
     * 获取分组列表
     * @return array
     */
    public static function getGroups($uid="")
    {
        $table = table("crongroup");
        $table->primary = "gid";
        $list=[];
        if (empty($uid)){
            $list = $table->gets(["order"=>"gid asc"]);
        }else{
            $t = table("group_user");
            $t->select = "*";
            $gids = $t->gets(["uid"=>$uid]);
            $tmp = [];
            foreach ($gids as $gid){
                $tmp[] = $gid["gid"];
            }
            if (!empty($tmp)){
                $list = $table->gets(["in"=>["gid",$tmp]]);
            }
        }

        if (empty($list)){
            $list = [];
        }else{
            $data = [];
            foreach ($list as $value){
                $data[$value["gid"]] = $value["gname"];
            }
            $list = $data;
        }
        return  $list;
    }

    /**
     * 获取单个分组
     * @param $gid
     * @return array
     */
    public static function getGroup($gid)
    {
        $table = table("crongroup");
        $table->primary = "gid";
        $data = $table->get($gid);
        if (!$data->exist()){
            return Util::errCodeMsg(101,"不存在");
        }
        $t = table("group_user");
        $t->select = "uid";
        $uids = $t->gets(["gid"=>$gid]);
        $da = [];
        foreach ($uids as $v){
            $da[] = $v["uid"];
        }
        return Util::errCodeMsg(0,"",["gid"=>$gid,"gname"=>$data["gname"],"uids"=>$da]);
    }

    /**
     * 添加分组
     * @param $group
     * @return array
     */
    public static function addGroup($group)
    {
        if (empty($group)) return Util::errCodeMsg(101,"参数为空");
        $table = table("crongroup");
        $table->primary = "gid";
        $uids = $group["uids"];
        unset($group["uids"]);
        if (!($gid =$table->put($group))){
            return Util::errCodeMsg(102,"添加失败");
        }
        $t = table("group_user");
        foreach ($uids as $uid){
            $t->put(["gid"=>$gid,"uid"=>$uid]);
        }
        return Util::errCodeMsg(0,"保存成功",$gid);
    }

    /**
     * 修改分组
     * @param $gid
     * @param $group
     * @return array
     */
    public static function updateGroup($gid,$group)
    {
        if (empty($gid) || empty($group)) return Util::errCodeMsg(101,"参数为空");
        $table = table("crongroup");
        $table->primary = "gid";
        $uids=[];
        if (isset($group["uids"])){
            $uids = $group["uids"];
            unset($group["uids"]);
        }
        if (!$table->set($gid,$group)){
            return Util::errCodeMsg(102,"更新失败");
        }
        $t = table("group_user");
        $t->dels(["gid"=>$gid]);
        foreach ($uids as $uid){
            $t->put(["gid"=>$gid,"uid"=>$uid]);
        }
        return Util::errCodeMsg(0,"更新成功",$gid);
    }

    /**
     * 删除分组
     * @param $gid
     * @return array
     */
    public static function deleteGroup($gid)
    {
        if (empty($gid)) return Util::errCodeMsg(101,"参数为空");
        if (table("crontab")->count(["gid"=>$gid]) > 0){
            return Util::errCodeMsg(101,"该分组下有定时任务，不能删除");
        }
        $table = table("crongroup");
        $table->primary = "gid";
        if (!$table->del($gid)){
            return Util::errCodeMsg(102,"删除失败");
        }
        $t = table("group_user");
        $t->dels(["gid"=>$gid]);
        return Util::errCodeMsg(0,"删除成功");
    }



    /**
     * 获取任务列表
     * @return array
     */
    public static function getList($gets=[],$page=1,$pagesize=10)
    {
        
        //页数
        if (!empty($pagesize))
        {
            $gets['pagesize'] = intval($pagesize);
        }
        else
        {
            $gets['pagesize'] = 20;
        }
        $gets['page'] = !empty($page) ? $page : 1;
        $pager="";
        $list =   table("crontab")->gets($gets, $pager);
        $tasks = LoadTasks::getTasks();
        $group = self::getGroups();
        foreach ($list as &$task)
        {
            $tmp = $tasks->get($task["id"]);
            $task["runStatus"] = $tmp["runStatus"];
            $task["runTimeStart"] = $tmp["runTimeStart"];
            $task["runUpdateTime"] = $tmp["runUpdateTime"];
            if (isset($group[$task["gid"]])){
                $task["gname"] = $group[$task["gid"]];
            }
        }
        return  ["total"=>$pager->total,"rows"=>$list];
    }

    /**
     * 获取单个任务
     * @param $id
     * @return array
     */
    public static function get($id)
    {
        $tasks = table("crontab");
        $task = $tasks->get($id);
        if (!$task->exist($id)){
            return Util::errCodeMsg(101,"不存在");
        }
        $data["id"] = $id;
        $data["gid"] = $task["gid"];
        $data["taskname"] = $task["taskname"];
        $data["rule"] = $task["rule"];
        $data["runnumber"] = $task["runnumber"];
        $data["execute"] = $task["execute"];
        $data["status"] = $task["status"];
        $data["runuser"] = $task["runuser"];
        $data["manager"] = $task["manager"];
        $data["agents"] = $task["agents"];
        $group = self::getGroups();
        if (isset($group[$task["gid"]])){
            $data["gname"] = $group[$task["gid"]];
        }
        return Util::errCodeMsg(0,"",$data);
    }

    /**
     * 添加任务
     * @param $task
     * @return array
     */
    public static function add($task)
    {
        if (empty($task)) return Util::errCodeMsg(101,"参数为空");
        $task["id"] = Donkeyid::getInstance()->dk_get_next_id();
        $ids = LoadTasks::saveTasks([$task]);
        if ($ids === false){
            return Util::errCodeMsg(102,"添加失败");
        }
        return Util::errCodeMsg(0,"保存成功",$ids);
    }

    /**
     *  修改任务
     * @param $id
     * @param $task
     * @return array
     */
    public static function update($id,$task)
    {
        if (empty($id) || empty($task)) return Util::errCodeMsg(101,"参数为空");
        if (!LoadTasks::updateTask($id,$task)){
            return ["code"=>102,"msg"=>"更新失败"];
        }
        return Util::errCodeMsg(0,"更新成功");
    }

    /**
     * 删除任务
     * @param $id
     * @return array
     */
    public static function delete($id)
    {
        if (empty($id)) return Util::errCodeMsg(101,"参数为空");
        if (!LoadTasks::delTask($id)){
            return Util::errCodeMsg(102,"删除失败");
        }
        return Util::errCodeMsg(0,"删除成功");
    }


    /**
     * 获取即将运行和已经运行的任务
     */
    public static function getRuntimeTasks($page=1,$size=20)
    {
        $start = ($page-1)*$size;
        $end = $start+$size;
        $data = [];
        $list = \Lib\Tasks::$table;
        $tasks = LoadTasks::getTasks();
        $n=0;
        foreach ($list as $id=>$rb)
        {
            $n++;
            if ($n <= $start) continue;
            if ($n > $end) break;
            $tmp = $tasks->get($rb["id"]);
            $rb["taskname"] = $tmp["taskname"];
            $data[$id] = $rb;
        }
        return  ["total"=>count($list),"rows"=>$data];
    }

}