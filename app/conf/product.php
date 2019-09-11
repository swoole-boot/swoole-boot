<?php

use cockroach\extensions\EArray;

/**
 * 引入公共配置
 */
$common = require __DIR__ . '/common.php';

$product = [

];

return EArray::merge($common, $product);
