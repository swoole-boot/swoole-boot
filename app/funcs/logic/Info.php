<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EReturn;
use cockroach\validators\Min;
use cockroach\validators\Required;

/**
 * Class Info
 * @package app\funcs\logic
 * @datetime 2019/9/19 18:32
 * @author roach
 * @email jhq0113@163.com
 */
class Info extends Logic
{
    /**
     * @return array
     * @datetime 2019/9/19 18:33
     * @author roach
     * @email jhq0113@163.com
     */
    public function rules()
    {
        return [
            [ ['id'], Required::class ,'msg' => 'id必传' ],
            [ ['id'], Min::class , 'min' => 1, 'msg' => 'id格式错误' ],
        ];
    }

    /**
     * @return mixed|void
     * @datetime 2019/9/19 18:38
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $data = User::self()->info($this->params['id']);
        return EReturn::success([
            'info' => $data
        ]);
    }
}