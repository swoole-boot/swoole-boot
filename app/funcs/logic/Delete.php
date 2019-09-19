<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
use cockroach\validators\Required;

/**
 * Class Delete
 * @package app\funcs\logic
 * @datetime 2019/9/19 19:21
 * @author roach
 * @email jhq0113@163.com
 */
class Delete extends Logic
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
     * @return array
     * @datetime 2019/9/19 19:23
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $id = EFilter::fInt('id',$this->params);
        User::self()->delete($id);
        return EReturn::success();
    }
}