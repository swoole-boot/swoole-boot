<?php

use cockroach\extensions\EArray;

/**
 * 引入公共配置
 */
$common = require __DIR__ . '/common.php';

$product = [
    'server' => [
        'components' => [
            'dispatcher' => [
                'register' => [
                    'dataCenter' => 'product',
                    //本地client节点
                    'localNode'   => 'http://127.0.0.1:8500',
                    //备用server节点
                    'standbyNode' => 'http://10.16.49.95:8500',
                    'token'       => '7028e52c-9ed3-1c8c-b81d-c35575456b45'
                ]
            ]
        ]
    ]
];

return EArray::merge($common, $product);
