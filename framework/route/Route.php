<?php
namespace boot\route;

use cockroach\base\Cockroach;

/**
 * Class Route
 * @package boot
 * @datetime 2019/9/12 13:12
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Route extends Cockroach
{
    /**
     * @param array $package
     * @return mixed
     * @datetime 2019/9/23 11:30
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function route(array $package);
}