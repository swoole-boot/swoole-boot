<?php
namespace app\funcs;

use cockroach\extensions\EReturn;

/**
 * Class Func
 * @package app\funcs
 * @datetime 2019/9/12 12:52
 * @author roach
 * @email jhq0113@163.com
 */
class Func extends \boot\Func
{
    /**
     * @return mixed|void
     * @datetime 2019/9/12 13:41
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        return EReturn::success('Hello World');
    }
}