<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-31
 * Time: 下午4:18
 */

namespace Lib;


class TermLog
{
    private static $key = "term";


    public static function flush()
    {
        \Swoole::$php->log(self::$key)->flush();
    }

    public static function log($value,$id="")
    {
        $text = "";
        if (!empty($id)){
            $text = $id."\t";
        }
        $text .= is_scalar($value) ? $value : json_encode($value);
        if (DEBUG == "on"){
            echo $text,"\n";
        }
        \Swoole::$php->log(self::$key)->info($text);
    }
}