<?php

/**
 * Created by PhpStorm.
 * User: ClownFish 187231450@qq.com
 * Date: 14-12-27
 * Time: 上午11:59
 */
class ParseCrontab
{
    static public $error;

    /**
     *  解析crontab的定时格式，linux只支持到分钟/，这个类支持到秒
     * @param string $crontab_string :
     *
     *      0     1    2    3    4    5
     *      *     *    *    *    *    *
     *      -     -    -    -    -    -
     *      |     |    |    |    |    |
     *      |     |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |     |    |    |    +----- month (1 - 12)
     *      |     |    |    +------- day of month (1 - 31)
     *      |     |    +--------- hour (0 - 23)
     *      |     +----------- min (0 - 59)
     *      +------------- sec (0-59)
     * @param int $start_time timestamp [default=current timestamp]
     * @return int unix timestamp - 下一分钟内执行是否需要执行任务，如果需要，则把需要在那几秒执行返回
     * @throws InvalidArgumentException 错误信息
     */
    static public function parse($crontab_string, $start_time = null)
    {
        if(is_array($crontab_string)){
            return self::_parse_array($crontab_string,$start_time);
        }
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontab_string))) {
            if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontab_string))) {
                self::$error = "Invalid cron string: " . $crontab_string;
                return false;
            }
        }
        if ($start_time && !is_numeric($start_time)) {
            self::$error = "\$start_time must be a valid unix timestamp ($start_time given)";
            return false;
        }
        $cron = preg_split("/[\s]+/i", trim($crontab_string));
        $start = empty($start_time) ? time() : $start_time;

        if (count($cron) == 6) {
            $date = array(
                'second'  => self::_parse_cron_number($cron[0], 0, 59),
                'minutes' => self::_parse_cron_number($cron[1], 0, 59),
                'hours'   => self::_parse_cron_number($cron[2], 0, 23),
                'day'     => self::_parse_cron_number($cron[3], 1, 31),
                'month'   => self::_parse_cron_number($cron[4], 1, 12),
                'week'    => self::_parse_cron_number($cron[5], 0, 6),
            );
        } elseif (count($cron) == 5) {
            $date = array(
                'second'  => array(1 => 1),
                'minutes' => self::_parse_cron_number($cron[0], 0, 59),
                'hours'   => self::_parse_cron_number($cron[1], 0, 23),
                'day'     => self::_parse_cron_number($cron[2], 1, 31),
                'month'   => self::_parse_cron_number($cron[3], 1, 12),
                'week'    => self::_parse_cron_number($cron[4], 0, 6),
            );
        }
        if (
            in_array(intval(date('i', $start)), $date['minutes']) &&
            in_array(intval(date('G', $start)), $date['hours']) &&
            in_array(intval(date('j', $start)), $date['day']) &&
            in_array(intval(date('w', $start)), $date['week']) &&
            in_array(intval(date('n', $start)), $date['month'])

        ) {
            return $date['second'];
        }
        return null;
    }

    /**
     * 解析单个配置的含义
     * @param $s
     * @param $min
     * @param $max
     * @return array
     */
    static protected function _parse_cron_number($s, $min, $max)
    {
        $result = array();
        $v1 = explode(",", $s);
        foreach ($v1 as $v2) {
            $v3 = explode("/", $v2);
            $step = empty($v3[1]) ? 1 : $v3[1];
            $v4 = explode("-", $v3[0]);
            $_min = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
            $_max = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);
            for ($i = $_min; $i <= $_max; $i += $step) {
                $result[$i] = intval($i);
            }
        }
        ksort($result);
        return $result;
    }

    static protected function _parse_array($crontab_array,$start_time){
        $result = array();
        foreach($crontab_array as $val){
            $time = strtotime($val);
            if($time > $start_time && $time <=$start_time+60){
                $sec = date("s",$time);
                $result[intval($sec)] = intval($sec);
            }
        }
        ksort($result);
        return $result;
    }
}