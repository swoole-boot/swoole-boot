<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
use cockroach\validators\Min;

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
            [ ['id'], Min::class, 'type' => EFilter::TYPE_INT, 'min' => 1, 'msg' => 'id必传' ]
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
        User::self()->delete($this->params['id']);
        return EReturn::success();
    }
}