<?php
namespace app\funcs\logic;

use app\funcs\Func;
use cockroach\validators\Length;
use cockroach\validators\Phone;

/**
 * Class Logic
 * @package app\funcs\logic
 * @datetime 2019/9/19 18:30
 * @author roach
 * @email jhq0113@163.com
 */
class Logic extends Func
{
    /**
     * @var array
     * @datetime 2019/9/19 19:09
     * @author roach
     * @email jhq0113@163.com
     */
    public $rules = [
        [ ['phone'], Phone::class ],
        [ ['username'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '用户名长度不合法'],
        [ ['truename'], Length::class, 'max' => 255, 'min' => 6, 'msg' => '真实姓名长度不合法'],
    ];
}