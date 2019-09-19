<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/9/17
 * Time: 10:53 PM
 */
namespace boot\db;

/**
 * Class Query
 * @package boot\db
 * @datetime 2019/9/17 10:53 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Query extends \cockroach\orm\Query
{
    /**
     * @var Pool | Db
     * @datetime 2019/9/17 10:55 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $db;

    /**
     * @return array
     * @datetime 2019/9/18 11:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function all()
    {
        $sql = $this->sql();
        return $this->db->query($sql,$this->_params);
    }

    /**
     * @return array
     * @datetime 2019/9/18 11:27
     * @author roach
     * @email jhq0113@163.com
     */
    public function one()
    {
        $this->limit(1);
        $list = $this->all();
        return isset($list[0]) ? $list[0] : [];
    }

    /**
     * @return int
     * @datetime 2019/9/19 14:10
     * @author roach
     * @email jhq0113@163.com
     */
    public function count()
    {
        $info = $this->select('COUNT(*) AS `count`')->one();
        return isset($info['count']) ? (int)$info['count'] : 0;
    }
}