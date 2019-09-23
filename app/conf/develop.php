<?php
use cockroach\extensions\EArray;

/**
 * 引入公共配置
 */
$common = require __DIR__.'/common.php';

$develop = [
    'server' => [
        'components' => [
            'dispatcher' => [
                'register' => [
                    'dataCenter'  => 'develop',
                    //本地client节点
                    'localNode'   => 'http://10.16.49.95:8500',
                    //备用server节点
                    'standbyNode' => 'http://10.16.49.95:2500',
                    'token'       => '7028e52c-9ed3-1c8c-b81d-c35575456b77'
                ]
            ]
        ]
    ]
];

return EArray::merge($common,$develop);
