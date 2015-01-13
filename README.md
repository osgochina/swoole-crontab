Swoole-Crontab(基于Swoole扩展)
==============
1.概述
--------------
+ 基于swoole的定时器程序，支持秒级处理.  
+ 异步多进程处理。
+ 完全兼容crontab语法，且支持秒的配置
+ 请使用swoole扩展1.7.9以上版本.[Swoole](https://github.com/swoole/swoole-src)

2.配置的支持
--------------
具体配置文件请看 [src/config/dev/crontab.php](https://github.com/osgochina/swoole-crontab/blob/master/src/config/dev/crontab.php)   
介绍一下时间配置  

    0   1   2   3   4   5
    |   |   |   |   |   |
    |   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
    |   |   |   |   +------ month (1 - 12)
    |   |   |   +-------- day of month (1 - 31)
    |   |   +---------- hour (0 - 23)
    |   +------------ min (0 - 59)
    +-------------- sec (0-59)[可省略，如果没有0位,则最小时间粒度是分钟]
3.帮助信息
----------
    * Usage: /path/to/php main.php [options] -- [args...]

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
    * -r [--reload]      重新载入配置文件
    * -m [--monitor]     监控进程是否在运行,如果在运行则不管,未运行则启动进程
    * --http             开启http服务
    * --host             监听ip,默认是127.0.0.1
    * --port             监听端口.默认是9501
    * --checktime        是否精确对时(如果精确对时,程序则会延时到分钟开始0秒启动)

4.Http 服务
------------
你可以在启动的时候加上参数--http 来开启http服务，可以指定端口及ip     
开启了http服务以后，你可以使用以下API接口    
1).查看日志 

    http://127.0.0.1:9501/log?date=2015-01-12
    
2).查看任务列表
    
    http://127.0.0.1:9501/conf
    
3).添加修改任务
    
    http://127.0.0.1:9501/conf      [post]方式
    tasks={
              "taskid1": {
                  "name": "php -i", 
                  "time": "1 * * 8 * *", 
                  "task": {
                      "parse": "Cmd", 
                      "cmd": "php -i", 
                      "output": "/tmp/test.log"
                  }
              }, 
              "taskid2": {
                  "name": "php -i", 
                  "time": "* 42-43 * * * *", 
                  "task": {
                      "parse": "Cmd", 
                      "cmd": "php -i", 
                      "output": "/tmp/test.log"
                  }
              }
          }
    
4).删除任务

    http://127.0.0.1:9501/conf?taskid=taskid2      [delete]方式

5).批量导入(注意，批量导入会清空已存在的任务)

    http://127.0.0.1:9501/import                    [post]方式
    tasks={
                  "taskid1": {
                      "name": "php -i", 
                      "time": "1 * * 8 * *", 
                      "task": {
                          "parse": "Cmd", 
                          "cmd": "php -i", 
                          "output": "/tmp/test.log"
                      }
                  }, 
                  "taskid2": {
                      "name": "php -i", 
                      "time": "* 42-43 * * * *", 
                      "task": {
                          "parse": "Cmd", 
                          "cmd": "php -i", 
                          "output": "/tmp/test.log"
                      }
                  }
              }
    
    
 



5.例子
-----------
你可以在配置文件中加上以下配置:  

    return [
        "taskid1"=>[
            "name" => "php -i",
            "time" => '* * * * * *',
            "task" => [
                "parse"  => "Cmd",
                "cmd"    => "php -i",
                "output" => "/tmp/test.log"
            ]
        ]
    ]
然后去到src目录下,执行  

    /path/to/php main.php -s start
    
执行完成以后你就可以在/tmp/test.log看到输出了，每秒输出一次

如果你需要写自己的代码逻辑，你也可以到plugin目录下，实现一个PluginBase.class.php接口的类.   
在其中写自己的逻辑代码。
