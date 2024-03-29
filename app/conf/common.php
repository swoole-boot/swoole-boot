<?php
return [
    'class'  => 'boot\Application',
    'name'   => 'swoole-boot',
    'host'   => '0.0.0.0',
    'port'   => 888,
    'server' => [
        //进程pid
        'pidFile' => '/var/swoole-boot.pid',
        //服务配置
        'setting' => [
            //工作进程数量，建议为cpu数量的4倍，一个进程占用内存可以按照40M估算
            'worker_num' => 16,
            'max_request' => 200,
            'user'        => 'www',
            'group'       => 'www'
        ],
        //组件
        'components' => [
            //日志
            'logger' => [
                'class'    => 'cockroach\log\Seaslog',
                'app'      => 'boot',
                'basePath' => '/tmp/logs/swoole-boot'
            ],
            //调度器
            'dispatcher' => [
                'class' => 'boot\dispatcher\SwooleBoot',
                'autoRegister' => true,
                //服务注册器
                'register' => [
                    'class' => 'cockroach\consul\Client',
                ],
                //'registerName' => 'boot',
                //'registerNode' => '10.16.49.95',
                'registerHost' => '10.16.49.95',
                'registerPort' => 2088,
                'packager' => [
                    'class' => 'cockroach\packages\SwooleBoot'
                ]
            ],
            //路由
            'router'   => [
                'class'  => 'boot\route\SwooleBoot',
            ],
            //数据库
            'db' => [
                'class' => 'boot\db\Pool',
                'masterConfig' => [
                    'class'     => 'boot\db\Mysql',
                    'host'      => '127.0.0.1',
                    'port'      => 3306,
                    'user'      => 'boot',
                    'password'  => 'adfassdfasdfassdf',
                    'database'  => 'boot',
                ]
            ]
        ]
    ]
];