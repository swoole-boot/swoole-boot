<?php
namespace boot\db;

use cockroach\base\Cockroach;

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
     * @datetime 2019/9/17 19:02
     * @author roach
     * @email jhq0113@163.com
     */
    static public function insert($columns=[])
    {

    }

    static public function multiInsert($rows)
    {
        $fields = [];

        foreach ($rows[0] as $field => $value) {
            array_push($fields,'`'.$field.'`');
        }

        $placeHolder = [];
        foreach ($columns as $field => $value) {

        }
        $sql = 'INSERT INTO `'.static::$tableName.'`('.$fields.')VALUES'.implode(',',);
        $db = static::getDb()->execute();
    }
}