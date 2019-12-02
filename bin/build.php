#!/usr/local/bin/php
<?php
if (empty($argv[1]))
{
    $dst = 'agent';
}
else
{
    $dst = trim($argv[1]);
}

if ($dst == 'agent')
{
    $pharFile = __DIR__ . '/agent.phar';
    if (file_exists($pharFile)) unlink($pharFile);
    $phar = new Phar($pharFile);
    $phar->buildFromDirectory(dirname(__DIR__) . "/src/agent/");
    //$phar->compressFiles(\Phar::GZ);
    $phar->stopBuffering();
    $phar->setStub($phar->createDefaultStub('agent.php'));
    echo "agent.phar打包成功\n";
}
elseif ($dst == 'centre')
{
    $pharFile = __DIR__ . '/crontab-centre.phar';
    if (file_exists($pharFile)) unlink($pharFile);
    $phar = new Phar($pharFile);
    $phar->buildFromDirectory(dirname(__DIR__) . "/src/center/");
    //$phar->compressFiles(\Phar::GZ);
    $phar->stopBuffering();
    $phar->setStub($phar->createDefaultStub('centre.php'));
    echo "crontab-center.phar打包成功\n";
}
elseif ($dst == 'phar')
{
    if (empty($argv[2]))
    {
        die("使用方法：php {$argv[0]} {$argv[1]} 源码目录\n");
    }

    if (!is_dir($argv[2]))
    {
        die("目录({$argv[2]})不存在\n");
    }
    $dirname = basename($argv[2]);
    $filename = $dirname . '.phar';
    $pharFile = __DIR__ . '/'.$filename;
    $phar = new Phar($pharFile);
    $phar->buildFromDirectory($argv[2]);
    $phar->compressFiles(\Phar::GZ);
    $phar->stopBuffering();
    $phar->setStub($phar->createDefaultStub('main.php'));
    echo "{$filename}打包成功\n";
}