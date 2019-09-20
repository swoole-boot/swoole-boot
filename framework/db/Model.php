<?php
namespace boot\db;

use boot\Application;
use cockroach\base\Cockroach;
use cockroach\base\Container;

/**
 * Class Model
 * @package boot\db
 * @datetime 2019/9/17 18:58
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Model extends Cockroach
{
    /**
     * @var string $tableName 表名称
     * @datetime 2019/9/17 19:01
     * @author roach
     * @email jhq0113@163.com
     */
    public static $tableName;

    /**主键
     * @var string
     * @datetime 2019/9/17 19:01
     * @author roach
     * @email jhq0113@163.com
     */
    public static $primaryKey = 'id';

    /**返回一个Db或者连接池
     * @return Pool | Db
     * @datetime 2019/9/17 18:59
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public static function getDb();

    /**
     * @param array $columns
     * @param bool  $ignore
     * @return ExecuteResult
     * @datetime 2019/9/17 10:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function insert($columns= [], $ignore = false)
    {
        return static::multiInsert([$columns], $ignore);
    }

    /**
     * @param array $rows
     * @param bool  $ignore
     * @return ExecuteResult
     * @datetime 2019/9/17 10:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function multiInsert($rows, $ignore = false)
    {
        $params = [];
        $sql = Query::multiInsert(static::$tableName,$rows,$params,$ignore);
        return static::getDb()->execute($sql,$params);
    }

    /**
     * @param array|string $set
     * @param array|string $where
     * @param bool         $isOr
     * @return ExecuteResult
     * @datetime 2019/9/17 10:43 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function updateAll($set, $where, $isOr = false)
    {
        $params = [];
        $sql = Query::updateAll(static::$tableName,$set,$where,$params,$isOr);
        return static::getDb()->execute($sql,$params);
    }

    /**
     * @param array|string $where
     * @param bool         $isOr
     * @return ExecuteResult
     * @datetime 2019/9/17 10:49 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function deleteAll($where, $isOr = false)
    {
        $params = [];
        $sql = Query::deleteAll(static::$tableName,$where,$params,$isOr);
        return static::getDb()->execute($sql,$params);
    }

    /**
     * @param bool $useMaster
     * @return array|mixed
     * @datetime 2019/9/19 14:19
     * @author roach
     * @email jhq0113@163.com
     */
    static public function find($useMaster = false)
    {
        $db = static::getDb();
        //如果是连接池的话支持强制主库查询，数据库连接不存在主从
        if($useMaster && $db instanceof Pool) {
            $db = $db->get($useMaster);
        }

        return Container::insure([
            'class' => 'boot\db\Query',
            'table' => static::$tableName,
            'db'    => $db
        ]);
    }
}