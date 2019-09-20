<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
use cockroach\validators\Length;
use cockroach\validators\Phone;
use cockroach\validators\Required;

/**
 * Class Update
 * @package app\funcs\logic
 * @datetime 2019/9/19 19:03
 * @author roach
 * @email jhq0113@163.com
 */
class Update extends Logic
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
            [ ['id'], Required::class, 'msg' => 'id必传'],
            [ ['phone'], Phone::class, 'allowNull' => true],
            [ ['username'], Length::class, 'allowNull' => true, 'max' => 255, 'min' => 6, 'msg' => '用户名长度不合法'],
            [ ['truename'], Length::class, 'allowNull' => true, 'max' => 255, 'min' => 6, 'msg' => '真实姓名长度不合法'],
        ];
    }

    /**
     * @return array|mixed|void
     * @datetime 2019/9/19 19:20
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $id = EFilter::fInt('id',$this->params);
        $user = User::self();
        $info = $user->info($id);
        if(empty($info)) {
            return EReturn::error('用户不存在',EReturn::ERROR_PARAMS);
        }

        $user->updateWithLock([
            'username' => EFilter::fStr('username',$this->params,$info['username']),
            'truename' => EFilter::fStr('truename',$this->params,$info['truename']),
            'phone'   => EFilter::fStr('phone',$this->params,$info['phone']),
        ],$id);

        return EReturn::success([
            'info' => $user->info($id)
        ]);
    }
}