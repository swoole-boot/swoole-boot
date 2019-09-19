<?php
namespace app\funcs\v1;

use app\logic\User;
use cockroach\extensions\EReturn;

/**
 * Class Logic
 * @package app\funcs\v1
 * @datetime 2019/9/19 15:02
 * @author roach
 * @email jhq0113@163.com
 */
class Logic extends V1
{
    /**
     * @return mixed|void
     * @datetime 2019/9/19 15:05
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $user = new User();
        $data = $user->index();
        return EReturn::success($data);
    }
}