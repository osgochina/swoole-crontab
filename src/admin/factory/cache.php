<?php
$configs = \Swoole::$php->config['cache'];
if (empty($configs[\Swoole::$php->factory_key]))
{
    throw new Swoole\Exception\Factory("cache->".\Swoole::$php->factory_key." is not found.");
}
return Swoole\Factory::getCache(Swoole::$php->factory_key);