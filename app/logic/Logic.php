<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/9/15
 * Time: 9:11 PM
 */

namespace app\logic;

use boot\db\ExecuteResult;
use boot\db\Query;
use cockroach\base\Cockroach;

/**业务逻辑基类
 * Class Logic
 * @package app\logic
 * @datetime 2019/9/15 9:11 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Logic extends Cockroach
{
    /**
     * @var static
     * @datetime 2019/9/19 18:35
     * @author roach
     * @email jhq0113@163.com
     */
    static protected $_instance;

    /**
     * @return static
     * @datetime 2019/9/19 18:37
     * @author roach
     * @email jhq0113@163.com
     */
    static public function self()
    {
        if(!isset(static::$_instance)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @var string
     * @datetime 2019/9/19 13:47
     * @author roach
     * @email jhq0113@163.com
     */
    public $modelClass;

    /**
     * @param bool $useMaster 是否使用主库
     * @return Query
     * @datetime 2019/9/19 14:21
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _getQuery($useMaster = false)
    {
        return call_user_func($this->modelClass.'::find', $useMaster);
    }

    /**添加数据，返回刚刚插入数据的id
     * @param array $data
     * @return int
     * @datetime 2019/9/19 14:37
     * @author roach
     * @email jhq0113@163.com
     */
    public function create($data = [])
    {
        /**
         * @var ExecuteResult $result
         */
        $result = call_user_func($this->modelClass.'::insert', $data);
        return $result->lastInsertId;
    }

    /**检索一个
     * @param array $where
     * @param array $group
     * @param array $order
     * @param bool $useMaster
     * @return array
     * @datetime 2019/9/20 11:39
     * @author roach
     * @email jhq0113@163.com
     */
    public function findOne($where = [], $group = [], $order = [], $useMaster = false)
    {
        return $this->_getQuery($useMaster)->where($where)->group($group)->order($order)->one();
    }

    /**检索多个
     * @param array $where
     * @param array $group
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @param bool $useMaster
     * @return array
     * @datetime 2019/9/20 11:40
     * @author roach
     * @email jhq0113@163.com
     */
    public function findAll($where = [], $group = [], $order = [], $offset = 0, $limit = 1000,$useMaster = false)
    {
        return $this->_getQuery($useMaster)->where($where)->group($group)->order($order)->offset($offset)->limit($limit)->all();
    }

    /**获取详情
     * @param int   $id
     * @param bool  $useMaster
     * @return array
     * @datetime 2019/9/19 14:22
     * @author roach
     * @email jhq0113@163.com
     */
    public function info($id, $useMaster = false)
    {
        return $this->_getQuery($useMaster)->where([
            'id'=> $id
            ])
            ->one();
    }

    /**分页查询
     * @param array|string $select
     * @param array        $where
     * @param array        $order
     * @param int          $page
     * @param int          $pageSize
     * @param bool         $useMaster
     * @return array
     * @datetime 2019/9/19 14:17
     * @author roach
     * @email jhq0113@163.com
     */
    public function index($select = '*',$where = [], $order = [], $page = 1, $pageSize = 15, $useMaster = false)
    {
        $data = [
            'page'       => $page,
            'pageSize'   => $pageSize,
            'total'      => 0,
            'totalPage'  => 0,
            'list'       => []
        ];

        $data['page'] = ($page < 2) ? 1 : $page;
        $pageSize = ($pageSize < 5) ? 5 : $pageSize;
        $data['pageSize'] = ($pageSize > 1000) ? 1000 : $pageSize;

        $query = $this->_getQuery($useMaster);

        $data['total'] = $query->where($where)
            ->count();

        if($data['total'] < 1) {
            return $data;
        }

        $data['totalPage'] = ceil($data['total'] / $data['pageSize']);
        if($data['page'] > $data['totalPage']) {
            $data['page'] = $data['totalPage'];
        }
        $offset = ($data['page']-1) * $data['pageSize'];

        $data['list'] = $query->select($select)
            ->order($order)
            ->offset($offset)
            ->limit($data['pageSize'])
            ->all();

        return $data;
    }

    /**带乐观锁锁更新，建议使用，表中需要有`version`字段
     * @param array $set
     * @param int   $id
     * @return int
     * @datetime 2019/9/20 11:24
     * @author roach
     * @email jhq0113@163.com
     */
    public function updateWithLock($set,$id)
    {
        $query = $this->_getQuery(true);
        $info = $query->where([ 'id' => $id])->one();

        if(empty($info)) {
           return 0;
        }

        return $this->update($set, $id, $info['version']);
    }

    /**
     * @param array $set
     * @param int   $id
     * @param null  $lockVersion 乐观锁版本，需要数据库表中有version字段
     * @return int
     * @datetime 2019/9/19 14:30
     * @author roach
     * @email jhq0113@163.com
     */
    public function update($set, $id, $lockVersion = null)
    {
        $where = [
            'id' => $id
        ];

        //如果使用乐观锁
        if(!is_null($lockVersion)) {
            $newVersion = (int)$lockVersion + 1;

            if(is_array($set) && !isset($set['version'])) {
                $set['version'] = $newVersion;
            }else {
                if(strpos($set,'version') === false) {
                    $set .=',`version`='.$newVersion;
                }
            }

            $where['version'] = $lockVersion;
        }

        /**
         * @var ExecuteResult $result
         */
        $result = call_user_func($this->modelClass.'::updateAll', $set, [ 'id' => $id ]);
        return $result->affectedRows;
    }

    /**软删除或放到垃圾箱，需要数据库表中有is_on字段
     * @param int $id
     * @return int
     * @datetime 2019/9/19 14:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function remove($id)
    {
        /**
         * @var ExecuteResult $result
         */
        $result = call_user_func($this->modelClass.'::updateAll', [ 'is_on'=> '0' ], [ 'id' => $id ]);
        return $result->affectedRows;
    }

    /**物理删除
     * @param int   $id
     * @return int
     * @datetime 2019/9/19 14:28
     * @author roach
     * @email jhq0113@163.com
     */
    public function delete($id)
    {
        /**
         * @var ExecuteResult $result
         */
        $result = call_user_func($this->modelClass.'::deleteAll', [ 'id' => $id ]);
        return $result->affectedRows;
    }
}