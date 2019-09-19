<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;

/**
 * Class Create
 * @package app\funcs\logic
 * @datetime 2019/9/19 19:07
 * @author roach
 * @email jhq0113@163.com
 */
class Create extends Logic
{
    /**
     * @return array
     * @datetime 2019/9/19 19:13
     * @author roach
     * @email jhq0113@163.com
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * @return array|mixed|void
     * @datetime 2019/9/19 19:15
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $userId = User::self()->create([
            'username' => EFilter::fStr('username',$this->params),
            'truename' => EFilter::fStr('truename',$this->params),
            'phone' => EFilter::fStr('phone',$this->params),
        ]);

        return EReturn::success([
            'id' => $userId
        ]);
    }
}