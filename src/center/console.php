<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-10-20
 * Time: 下午2:18
 */
require_once __DIR__ . '/_init.php';
function cleanLogs()
{
    $datetime = date("Y-m-d", strtotime(date("Y-m-d") . "-1 month"));
    $db = table("term_logs");
    if (!$db->dels(["where" => ["createtime<'" . $datetime . "'"]])) {
        return Lib\Util::errCodeMsg(1, "删除失败");
    }
    return Lib\Util::errCodeMsg(0, "删除成功");
}
$ret = cleanLogs();
var_dump($ret);