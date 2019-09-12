<?php
return [
    'class'  => 'boot\Application',
    'host'   => '0.0.0.0',
    'port'   => 888,
    'server' => [
        'pidFile' => '/var/swoole-boot.pid',
        //路由
        'router'   => [
            'class'  => 'boot\route\SwooleBoot',
            'logger' => [
                'class' => 'cockroach\log\Seaslog'
            ],
            'packager' => [
                'class' => 'cockroach\packages\SwooleBoot'
            ]
        ],
        //服务配置
        'setting' => [

        ]
    ]
];