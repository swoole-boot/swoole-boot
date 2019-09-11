<?php
use cockroach\extensions\EArray;

/**
 * 引入公共配置
 */
$common = require __DIR__.'/common.php';

$develop = [

];

return EArray::merge($common,$develop);
