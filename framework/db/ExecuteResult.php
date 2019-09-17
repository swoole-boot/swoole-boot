<?php
namespace boot\db;
use cockroach\base\Cockroach;

/**
 * Class ExecuteResult
 * @package boot\db
 * @datetime 2019/9/17 18:36
 * @author roach
 * @email jhq0113@163.com
 */
class ExecuteResult extends Cockroach
{
    /**受影响行数
     * @var int
     * @datetime 2019/9/17 18:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $affectedRows = 0;

    /**插入的id
     * @var int
     * @datetime 2019/9/17 18:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $lastInsertId;

    /**
     * @var string
     * @datetime 2019/9/17 18:38
     * @author roach
     * @email jhq0113@163.com
     */
    public $error = '';

    /**
     * @var int
     * @datetime 2019/9/17 18:38
     * @author roach
     * @email jhq0113@163.com
     */
    public $errno = 0;
}