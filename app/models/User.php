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
    /**表结构，仅供示例
        CREATE TABLE `userinfo` (
            `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
            `username` varchar(64) NOT NULL DEFAULT '',
            `truename` varchar(32) DEFAULT NULL,
            `phone` bigint(11) DEFAULT NULL,
            `is_on` bigint(11) NOT NULL DEFAULT 1,
            `version` int(10) NOT NULL DEFAULT 0,
            `addtime` datetime DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `phone` (`phone`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     * /

    /**
     * @var string
     * @datetime 2019/9/19 14:41
     * @author roach
     * @email jhq0113@163.com
     */
    public static $tableName = 'userinfo';

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