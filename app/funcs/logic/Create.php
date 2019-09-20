<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
use cockroach\validators\Length;
use cockroach\validators\Phone;

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
        return [
            [ ['phone'], Phone::class],
            [ ['username'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '用户名长度不合法'],
            [ ['truename'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '真实姓名长度不合法'],
        ];
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

        if($userId < 1) {
            return EReturn::error('添加失败',EReturn::ERROR_PARAMS);
        }

        return EReturn::success([
            'info' => User::self()->info($userId,true)
        ]);
    }
}