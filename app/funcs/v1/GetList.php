<?php
namespace app\funcs\v1;

use cockroach\validators\Between;
use cockroach\validators\Callback;
use cockroach\validators\Email;
use cockroach\validators\Ip;
use cockroach\validators\Length;
use cockroach\validators\Max;
use cockroach\validators\Number;
use cockroach\validators\Pattern;
use cockroach\validators\Phone;
use cockroach\validators\Required;
use cockroach\validators\Url;

/**
 * Class GetList
 * @package app\funcs
 * @datetime 2019/9/11 13:55
 * @author roach
 * @email jhq0113@163.com
 */
class GetList extends V1
{
    /**
     * @return array
     * @datetime 2019/9/18 11:36 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function rules()
    {
        return [
            [ ['id'], Required::class, 'msg' => 'id必传'],
            [ ['name','nickname'], Length::class, 'max' => 100, 'min' => 20, 'msg' => '长度必须为[20,100]'],
            [ ['ip'], Ip::class],
            [ ['email'], Email::class , 'require' => true ],
            [ ['age'], Max::class, 'require' => true, 'max'=> 56 , 'msg'=> '年龄最大为56'],
            [ ['sex'], Between::class, 'require' => false, 'min' => 0, 'max' => 1 , 'msg'=> 'sex只能为0或1'],
            [ ['logo','imgUrl'], Url::class, 'require' => false, 'msg'=> '不是一个有效的图片'],
            [ ['mobile'], Phone::class, 'require' => false, 'msg' => '手机号码格式错误'],
            [ ['order'], Number::class, 'require' => false, 'msg' => '订单号必须全是数字'],

            [ ['call'], Callback::class, 'require' => false, function($data){
                return false;
            } ,'msg' => '你就是没通过'],
            [ ['patter'],Pattern::class, 'pattern'=> '/^((1[3|4|5|6|7|8|9][0-9]))\d{8}$/', 'msg'=>'正则匹配未通过']
        ];
    }

    /**
     * @return mixed|string|void
     * @datetime 2019/9/12 14:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        return $this->params;
    }
}