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

    static $_instance;

    public static function getInstance()
    {
        if (empty(self::$_instance))
        {
            self::$_instance = new FileLog(['file' => getRunPath() . '/logs/agent'.PORT.'.log']);
        }
        return self::$_instance;
    }


    public static function flush()
    {
        self::getInstance()->flush();
    }

    /**
     *
     */
    public static function log($value)
    {
        $text="";
        $text .= is_scalar($value) ? $value : json_encode($value);
        self::getInstance()->put($text);
    }

}