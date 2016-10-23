<?php
return [
    'site_name'=> 'CronTab 管理',
    'logo_url'=> '/static/smartadmin/img/logo-o.png',
    'login_url' => WEBROOT . '/Page/index',
    'logout_url' => WEBROOT . '/page/logout/',
    'home_url' => WEBROOT . '/crontab/index/',
    //忽视权限验证
    'RBAC_EXCLUDE'=>[
        "password"=>[
            "modifyPassword"=>true
        ]
    ]
];