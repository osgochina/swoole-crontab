<?php
/**
 * 记录运行日志
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 上午10:33
 */

namespace Lib;


class Flog
{
    private static $request_name = -1;
    private static $request_id = -1;
    private static $key = "master";


    public static function startLog($request_name, $key = "master")
    {
        self::$request_name = $request_name;
        self::$request_id = Donkeyid::getInstance()->dk_get_next_id();

        self::$key = $key;
    }

    public static function endLog()
    {
        self::$request_name = -1;
        self::$request_id = -1;
        self::$key = "master";
    }

    public static function flush()
    {
        \Swoole::$php->log(self::$key)->flush();
    }

    /**
     *
     */
    public static function log($value)
    {
        $text = "";
        if (self::$request_name != -1 && self::$request_id != -1) {
            $text = self::$request_id . "\t" . "[" . self::$request_name . "]" . "\t";
        }
        $text .= is_scalar($value) ? $value : json_encode($value);
        \Swoole::$php->log(self::$key)->info($text);
    }

}