<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午4:13
 */

namespace Lib;


class Util
{
    static function listenHost()
    {
        $listenHost = '127.0.0.1';
        if (ENV_NAME == 'product')
        {
            $iplist = swoole_get_local_ip();
            //监听局域网IP
            foreach ($iplist as $k => $v)
            {
                if (substr($v, 0, 7) == '192.168')
                {
                    $listenHost = $v;
                }
            }
        }
        else if (ENV_NAME == 'test')
        {
            $iplist = swoole_get_local_ip();
            //监听局域网IP
            foreach ($iplist as $k => $v)
            {
                if (substr($v, 0, 6) == '172.16')
                {
                    $listenHost = $v;
                }
            }
        }
        return $listenHost;
    }


    public static function errCodeMsg($code = 0, $message = '', $data = array())
    {
        $res = array(
            'code' => $code,
            'msg' =>  $message ? $message : ($code ? 'fail' : 'success'),
            'data' => $data
        );

        return $res;
    }
}