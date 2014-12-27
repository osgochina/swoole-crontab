swoole-crontab
==============

+ 基于swoole的定时器程序，支持秒级处理.
+ 异步多进程处理。
+ 完全兼容crontab语法，仅仅是多了一列'秒'配置
+ 配置文件请看src/config/dev/crontab.php


* 0 1 2 3 4 5
* | | | | | |
* | | | | | ------ day of week (0 - 6) (Sunday=0)
* | | | | ------ month (1 - 12)
* | | | -------- day of month (1 - 31)
* | | ---------- hour (0 - 23)
* | ------------ min (0 - 59)
* -------------- sec (0-59)
