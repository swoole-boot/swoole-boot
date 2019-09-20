<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;

/**
 * Class Index
 * @package app\funcs\logic
 * @datetime 2019/9/19 18:31
 * @author roach
 * @email jhq0113@163.com
 */
class Index extends Logic
{
    /**
     * @return array
     * @datetime 2019/9/19 18:31
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $data = User::self()->index('*',
            [
                'is_on' => 1
            ] ,
            [ 'id' => SORT_DESC ],
            EFilter::fInt('page',$this->params),
            EFilter::fInt('pageSize',$this->params)
        );
        return EReturn::success($data);
    }
}