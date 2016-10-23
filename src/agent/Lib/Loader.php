<?php
namespace Lib;

/**
 * Swoole库加载器
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage base
 *
 */
class Loader
{
	/**
	 * 命名空间的路径
	 */
	protected static $namespaces;

	/**
	 * 自动载入类
	 * @param $class
	 */
	static function autoload($class)
	{
		$root = explode('\\', trim($class, '\\'), 2);
		if (count($root) > 1 and isset(self::$namespaces[$root[0]]))
		{
            include self::$namespaces[$root[0]].'/'.str_replace('\\', '/', $root[1]).'.php';
		}
	}

	/**
	 * 设置根命名空间
	 * @param $root
	 * @param $path
	 */
	static function addNameSpace($root, $path)
	{
		self::$namespaces[$root] = $path;
	}
}