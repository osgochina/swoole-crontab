<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-7-18
 * Time: 上午11:47
 */

namespace App;


class RBAC
{

    /**
     * 获取现有代码的功能逻辑列表
     * @return array
     */
    public static function getPublicActionList()
    {
        $actionPath = dirname(dirname(__FILE__))."/controllers";
        $list = self::getReflectionList($actionPath);
        $pc = new ParserDoc();
        $RBAClist = [];
        foreach ($list as $key=>$ref){
            $classdoc = $ref->getDocComment();
            if (empty($classdoc)) continue;
            $classdoc = $pc->parse($classdoc);
            if (!isset($classdoc["RBAC"])) continue;
            $classRBAC = $classdoc["RBAC"];
            $RBAClist[$ref->name]["describe"] = $classRBAC;
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method){
                if ($method->class != $key){
                    continue;
                }
                $mothoddoc = $pc->parse($method->getDocComment());
                if (!isset($mothoddoc["RBAC"])) continue;
                $methodRBAC = $mothoddoc["RBAC"];
                $RBAClist[$ref->name]["methods"][$method->name]["describe"] = $methodRBAC;
            }
        }
        return $RBAClist;
    }
    private static function getReflectionList($actionPath)
    {
        $list = [];
        foreach (new \DirectoryIterator($actionPath) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if (!$fileInfo->isFile()) continue;
            include_once $fileInfo->getPath()."/".$fileInfo->getFilename();
            $cls = "App\\Controller\\".str_replace(".php","",$fileInfo->getFilename());
            if (!class_exists($cls)) continue;
            $list[$cls] = new \ReflectionClass($cls);
        }
        return $list;
    }

    /**
     * 获取用户有的权限
     * @param $userid
     * @return array
     */
    public static function loadAccess($userid)
    {
        $data = table("rbac_user_group")->db->query("select DISTINCT(n.node) from rbac_node as n WHERE n.gid IN(SELECT g.gid FROM rbac_user_group as ug LEFT JOIN rbac_group as g ON ug.gid=g.gid WHERE ug.userid='{$userid}');")->fetchall();
        if (empty($data)){
            return array();
        }
        $node = [];
        foreach ($data as $value){
            $node[strtoupper($value["node"])] = true;
        }
        return $node;
    }

    /**
     * 验证权限
     * @param $mvc
     * @return bool
     */
    public static function auth($mvc)
    {
        if ($_SESSION["user_id"] == 1){
            return true;
        }
        $exclude = \Swoole::$php->config["common"]["RBAC_EXCLUDE"];
        if (!empty($exclude)){
            if (isset($exclude[$mvc["controller"]])){
                $cks = $exclude[$mvc["controller"]];
                if ($cks == true) return true;
                $view = strtoupper($mvc["view"]);
                if (isset($cks[$view]) && $cks[$view] == true){
                    return true;
                }
            }
        }

        $_cls = strtoupper("App\\Controller\\".$mvc["controller"]);
        $rbac_list = isset($_SESSION["rbac_list"])?$_SESSION["rbac_list"]:[];
        if (isset($rbac_list[$_cls])){
            return true;
        }
        $_meth = $_cls."::".strtoupper($mvc["view"]);
        if (isset($rbac_list[$_meth])){
            return true;
        }
        return false;

    }

}