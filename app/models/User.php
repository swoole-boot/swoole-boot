<?php
namespace app\models;

use boot\Application;
use boot\db\Model;

/**
 * Class User
 * @package app\models
 * @datetime 2019/9/19 14:40
 * @author roach
 * @email jhq0113@163.com
 */
class User extends Model
{
    /**
     * @var string
     * @datetime 2019/9/19 14:41
     * @author roach
     * @email jhq0113@163.com
     */
    public static $tableName = 'user';

    /**
     * @return \boot\db\Pool
     * @datetime 2019/9/19 14:41
     * @author roach
     * @email jhq0113@163.com
     */
    static public function getDb()
    {
        return Application::$app->server->db;
    }
}