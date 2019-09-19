<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
use cockroach\validators\Required;

/**
 * Class Remove
 * @package app\funcs\logic
 * @datetime 2019/9/19 19:21
 * @author roach
 * @email jhq0113@163.com
 */
class Remove extends Logic
{
    /**
     * @return array
     * @datetime 2019/9/19 19:22
     * @author roach
     * @email jhq0113@163.com
     */
    public function rules()
    {
        return [
            ['id'], Required::class,'msg' => 'id必传'
        ];
    }

    /**
     * @return array|mixed|void
     * @datetime 2019/9/19 19:24
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $id = EFilter::fInt('id',$this->params);
        User::self()->remove($id);
        return EReturn::success();
    }
}