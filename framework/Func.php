<?php
namespace boot;

use cockroach\base\Cockroach;
use cockroach\extensions\EReturn;

/**
 * Class Func
 * @package boot
 * @datetime 2019/9/11 13:54
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Func extends Cockroach
{
    /**
     * @var mixed
     * @datetime 2019/9/12 13:09
     * @author roach
     * @email jhq0113@163.com
     */
    public $params;

    /**
     * @var string
     * @datetime 2019/9/12 13:57
     * @author roach
     * @email jhq0113@163.com
     */
    public $requestId;

    /**
     * @var string
     * @datetime 2019/9/12 13:57
     * @author roach
     * @email jhq0113@163.com
     */
    public $clientIp;

    /**
     * @return array
     * @datetime 2019/9/12 14:09
     * @author roach
     * @email jhq0113@163.com
     */
    public function beforeRun()
    {
        return EReturn::success('');
    }

    /**执行
     * @return mixed
     * @datetime 2019/9/12 13:06
     * @author roach
     * @email jhq0113@163.com
     */
    abstract function run();

    /**
     * @param $result
     * @return mixed
     * @datetime 2019/9/12 14:09
     * @author roach
     * @email jhq0113@163.com
     */
    public function afterRun($result)
    {
        return $result;
    }
}