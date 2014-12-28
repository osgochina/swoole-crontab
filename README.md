swoole-crontab
==============

+ 基于swoole的定时器程序，支持秒级处理.
+ 异步多进程处理。
+ 完全兼容crontab语法，仅仅是多了一列'秒'配置
+ 请使用swoole扩展1.7.9以上版本


+ 配置文件请看src/config/dev/crontab.php

* 0 1 2 3 4 5
* | | | | | |
* | | | | | ------ day of week (0 - 6) (Sunday=0)
* | | | | ------ month (1 - 12)
* | | | -------- day of month (1 - 31)
* | | ---------- hour (0 - 23)
* | ------------ min (0 - 59)
* -------------- sec (0-59)

> 帮助信息:
--------------------------------
> Usage: /path/to/php main.php [options] -- [args...]

* -h [--help]        显示帮助信息
* -p [--pid]         指定pid文件位置(默认pid文件保存在当前目录)
* -s start           启动进程
* -s stop            停止进程
* -s restart         重启进程
* -l [--log]         log文件夹的位置
* -c [--config]      config文件的位置(可以是文件,也可以是文件夹.
                     如果是文件,则载入指定文件.如果是文件夹,则载入文件夹
                     下的所有文件.)
* -d [--daemon]      是否后台运行
