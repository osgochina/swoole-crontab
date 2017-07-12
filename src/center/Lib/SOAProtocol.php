<?php

namespace Lib;

/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 2017/7/11
 * Time: 13:55
 */
class SOAProtocol
{
    protected static $encode_gzip = false;
    protected static $encode_json = false;

    const DECODE_PHP = 1;   //使用PHP的serialize打包
    const DECODE_JSON = 2;   //使用json_encode打包
    const DECODE_GZIP = 128; //启用GZIP压缩
    const HEADER_SIZE = 16;
    const HEADER_STRUCT = "Nlength/Ntype/Nuid/Nserid";
    const HEADER_PACK = "NNNN";

    /**
     * 编码
     * @param $function
     * @param $params
     * @param array $env
     * @return string
     */
    public static function encode($function, $params, $env = [])
    {
        $send = array('call' => $function, 'params' => $params);
        if (count($env) > 0) {
            //调用端环境变量
            $send['env'] = $env;
        }
        //请求串号
        $requestId = self::getRequestId();
        //打包格式
        $encodeType = self::$encode_gzip ? self::DECODE_JSON : self::DECODE_PHP;
        if (self::$encode_gzip) {
            $encodeType |= self::DECODE_GZIP;
        }

        return self::_encode($send, $encodeType, 0, $requestId);
    }

    /**
     * @param $data
     * @param int $type
     * @param int $uid
     * @param int $serid
     * @return string
     */
    protected static function _encode($data, $type = self::DECODE_PHP, $uid = 0, $serid = 0)
    {
        //启用压缩
        if ($type & self::DECODE_GZIP) {
            $_type = $type & ~self::DECODE_GZIP;
            $gzip_compress = true;
        } else {
            $gzip_compress = false;
            $_type = $type;
        }
        switch ($_type) {
            case self::DECODE_JSON:
                $body = json_encode($data);
                break;
            case self::DECODE_PHP:
            default:
                $body = serialize($data);
                break;
        }
        if ($gzip_compress) {
            $body = gzencode($body);
        }
        return pack(self::HEADER_PACK, strlen($body), $type, $uid, $serid) . $body;
    }

    /**
     * 解码
     * @param $data
     * @return mixed
     */
    public static function decode($data)
    {
        $header = unpack(self::HEADER_STRUCT, substr($data, 0, self::HEADER_SIZE));
        return self::_decode(substr($data, self::HEADER_SIZE), $header['type']);
    }

    /**
     * @param $data
     * @param int $unseralize_type
     * @return mixed
     */
    protected static function _decode($data, $unseralize_type = self::DECODE_PHP)
    {
        if ($unseralize_type & self::DECODE_GZIP) {
            $unseralize_type &= ~self::DECODE_GZIP;
            $data = gzdecode($data);
        }
        switch ($unseralize_type) {
            case self::DECODE_JSON:
                return json_decode($data, true);
            case self::DECODE_PHP;
            default:
                return unserialize($data);
        }
    }

    /**
     * 获取请求串id
     * @return int
     */
    protected static function getRequestId()
    {
        $us = strstr(microtime(), ' ', true);
        return intval(strval($us * 1000 * 1000) . rand(100, 999));
    }

    /**
     * 设置编码类型
     * @param $json
     * @param $gzip
     */
    public static function setEncodeType($json, $gzip)
    {
        if ($json) {
            self::$encode_json = true;
        }
        if ($gzip) {
            self::$encode_gzip = true;
        }
    }

}
