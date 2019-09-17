<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/9/17
 * Time: 10:53 PM
 */

namespace boot\db;

use cockroach\base\Cockroach;

/**
 * Class Query
 * @package boot\db
 * @datetime 2019/9/17 10:53 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Query extends Cockroach
{
    /**
     * @var string
     * @datetime 2019/9/17 10:55 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $table;

    /**
     * @var Pool | Db
     * @datetime 2019/9/17 10:55 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $db;

    /**
     * @var array|string
     * @datetime 2019/9/17 10:56 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_select = '*';

    /**
     * @param array | string $fields
     * @return $this
     * @datetime 2019/9/17 10:58 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function select($fields)
    {
        $this->_select = $fields;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     * @datetime 2019/9/17 10:57 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @var array|string
     * @datetime 2019/9/17 10:58 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_where;

    /**
     * @param array|string $where
     * @return $this
     * @datetime 2019/9/17 10:58 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function where($where)
    {
        $this->_where = $where;
        return $this;
    }

    /**
     * @var array|string
     * @datetime 2019/9/17 11:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_group;

    /**
     * @param array|string $group
     * @return $this
     * @datetime 2019/9/17 11:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function group($group)
    {
        $this->_group = $group;
        return $this;
    }

    /**
     * @var array|string
     * @datetime 2019/9/17 11:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_order;

    /**
     * @param array|string $order
     * @return $this
     * @datetime 2019/9/17 11:03 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function order($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * @var int
     * @datetime 2019/9/17 11:04 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_offset = 0;

    /**
     * @param int $offset
     * @return $this
     * @datetime 2019/9/17 11:05 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * @var int
     * @datetime 2019/9/17 11:06 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_limit = 1000;

    /**
     * @param int $limit
     * @return $this
     * @datetime 2019/9/17 11:06 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @datetime 2019/9/17 11:08 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function all()
    {
        $params = [];
        $sql = 'SELECT * FROM '.$this->table.' WHERE ';
        $this->db->query($sql,$params);
    }

    /**
     * @return array
     * @datetime 2019/9/17 11:07 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function one()
    {
        $this->limit(1);
        $list = $this->all();
        return isset($list[0]) ? $list[0] : [];
    }
}