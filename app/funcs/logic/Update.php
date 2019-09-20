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
            [ ['id'], Required::class, 'type' => EFilter::TYPE_INT, 'msg' => 'id必传'],
            [ ['phone'], Phone::class, 'type' => EFilter::TYPE_INT ],
            [ ['username'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '用户名长度不合法'],
            [ ['truename'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '真实姓名长度不合法'],
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
        $user = User::self();
        $info = $user->info($this->params['id']);
        if(empty($info)) {
            return EReturn::error('用户不存在',EReturn::ERROR_PARAMS);
        }

        $user->updateWithLock([
            'username' => $this->params['username'] ?? $info['username'],
            'truename' => $this->params['truename'] ?? $info['truename'],
            'phone'    => $this->params['phone']    ?? $info['phone'],
        ],$this->params['id']);

        return EReturn::success([
            'info' => $user->info($this->params['id'])
        ]);
    }
}