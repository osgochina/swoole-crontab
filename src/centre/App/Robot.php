<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-22
 * Time: 下午3:13
 */

namespace App;
use Lib\Robot as RB;

class Robot
{

    /**
     * 注册服务
     * @param $ip
     * @param $port
     * @return array
     */
    public static function register($ip,$port)
    {
        if(RB::register($ip,$port)){
            return ["code"=>0,"msg"=>"注册成功"];
        }
        return ["code"=>1,"msg"=>"注册失败"];
    }
}