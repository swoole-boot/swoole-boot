<?php
namespace boot\db;

use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\extensions\EString;

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
     * @param string $field
     * @return string
     * @datetime 2019/9/17 10:07 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function formatField($field)
    {
        return '`'.$field.'`';
    }

    /**
     * @param array|string $where
     * @param array        $params
     * @param bool         $isOr
     * @return string
     * @datetime 2019/9/17 10:46 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function analyWhere($where, &$params = [], $isOr = false)
    {
        if(empty($where)) {
            $where = '';
        }elseif (is_string($where)) {
            $where = ' WHERE '.$where;
        }elseif (is_array($where)) {
            $andWhere = [];
            foreach ($where as $field => $value) {
                if(is_array($value)) {
                    array_push($andWhere,static::formatField($field).'IN('.EString::repeatAndRTrim('?,',count($value)).')');
                    $params = array_merge($params,$value);
                }else {
                    array_push($params,$value);
                    array_push($andWhere,static::formatField($field).'=?');
                }
            }
            $where = ' WHERE '.implode(' '.($isOr ? 'OR' : 'AND').' ',$andWhere);
            unset($andWhere);
        }

        return $where;
    }

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
        $fields = array_map(function($field){
            return static::formatField($field);
        },array_keys($rows[0]));


        $placeHolder = '('.EString::repeatAndRTrim('?,',count($rows[0])).')';
        $placeHolder = EString::repeatAndRTrim($placeHolder.',',count($rows));

        $values       = [];
        foreach ($rows as $row) {
            array_merge($values,array_values($row));
        }

        $sql = 'INSERT '.($ignore ? 'IGNORE' :'').' INTO '.static::formatField(static::$tableName).'('.implode(',',$fields).')VALUES'.$placeHolder;
        return static::getDb()->execute($sql,$values);
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

        if(is_array($set)) {
            $sets = [];
            foreach ($set as $field => $value) {
                array_push($params,$value);
                array_push($sets,static::formatField($field).'=?');
            }

            $set = implode(',',$sets);
        }

        $sql = 'UPDATE `'.static::$tableName.'` SET '.$set.static::analyWhere($where,$params,$isOr);

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
        $sql = 'DELETE FROM '.static::formatField(static::$tableName).static::analyWhere($where,$params, $isOr);
        return static::getDb()->execute($sql,$params);
    }

    /**
     * @return Query
     * @datetime 2019/9/17 10:55 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function find()
    {
        return Container::insure([
            'class' => 'boot\db\Query',
            'table' => static::$tableName,
            'db'    => static::getDb()
        ]);
    }
}