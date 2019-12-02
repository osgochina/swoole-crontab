<?php
/**
 * 告警模块
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-9-19
 * Time: 上午11:17
 */

namespace Lib;

use Swoole;

class Report
{
    const report_url = "http://192.168.1.70:8080/service/alert";

    /**
     * 发送失败报警
     * @param $taskid
     * @param $runid
     * @return bool
     */
    static public function taskSendFailed($taskid, $runid)
    {
        $task = table("crontab")->get($taskid);
        $content = "发送任务[" . $task["taskname"] . "]失败,taskid." . $taskid . ",runid:" . $runid;
        if (ENV_NAME == "product" && !empty($task["manager"])) {
            self::alert($content, $task["manager"]);
        }
        Flog::log($content);
        return true;
    }

    /**
     * 执行失败报警
     * @param $taskid
     * @param $runid
     * @param $code
     * @return bool
     */
    static public function taskFailed($taskid, $runid, $code)
    {
        $task = table("crontab")->get($taskid);
        $content = "执行任务[" . $task["taskname"] . "]失败,taskid." . $taskid . ",runid:" . $runid . ",code:" . $code;
        if (ENV_NAME == "product" && !empty($task["manager"])) {
            self::alert($content, $task["manager"]);
        }
        Flog::log($content);
        return true;
    }

    static public function alert($content, $users)
    {
//        $url = vsprintf(self::report_url."?content=%s&users=%s",array($content,$users));
//        $curl = new Swoole\Client\CURL();
//        $ret = $curl->get($url);
//        Flog::log("告警信息返回结果".$ret);
    }
}