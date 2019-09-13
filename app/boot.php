<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/8/31
 * Time: 10:13 PM
 */

use cockroach\extensions\EEnvironment;
use cockroach\base\Container;
use boot\Application;

require __DIR__.'/conf/bootstrap.php';

/**
 * 获取配置文件
 */
$config = require __DIR__.'/conf/'.EEnvironment::envir().'.php';

/**
 * @var Application $app
 */
$app = Container::insure($config,Application::class);
$app->handler();

