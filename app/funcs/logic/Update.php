<?php
namespace app\funcs\logic;

use app\logic\User;
use cockroach\extensions\EFilter;
use cockroach\extensions\EReturn;
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
        return array_merge($this->rules,[
            ['id'], Required::class,'msg' => 'id必传'
        ]);
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

        $user->update([
            'username' => EFilter::fStr('username',$this->params,$info['username']),
            'truename' => EFilter::fStr('truename',$this->params,$info['truename']),
            'phone' => EFilter::fStr('phone',$this->params,$info['phone']),
        ],$id);

        return EReturn::success();
    }
}