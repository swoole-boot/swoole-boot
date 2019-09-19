<?php
namespace boot\db;

use boot\Application;
use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\log\ILog;
use Swoole\Coroutine\Channel;

/**
 * Class Pool
 * @package boot\db
 * @datetime 2019/9/17 18:22
 * @author roach
 * @email jhq0113@163.com
 */
class Pool extends Cockroach
{
    /**
     * @param array $config
     * @datetime 2019/9/19 14:52
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        $this->masterConfig = $config['masterConfig'];
        $this->slaveConfig  = $config['slaveConfig'] ?? $this->masterConfig;
        unset($config['masterConfig'],$config['slaveConfig']);

        //初始化日志
        if(!isset($config['logger'])) {
            $this->logger = Application::$app->server->logger;
        }

        parent::init($config);
    }

    /**
     * @var ILog
     * @datetime 2019/9/17 18:51
     * @author roach
     * @email jhq0113@163.com
     */
    public $logger = [];

    /**每个worker最大连接数
     * @var int
     * @datetime 2019/9/17 18:22
     * @author roach
     * @email jhq0113@163.com
     */
    public $max = 20;

    /**
     * @var array
     * @example [
     *      'class'     => 'boot\db\Mysql',
     *      'host'      => '127.0.0.1',
     *      'port'      => 3306,
     *      'user'      => 'cock',
     *      'password'  => 'roach',
     *      'database'  => 'cock',
     * ]
     * @datetime 2019/9/17 18:22
     * @author roach
     * @email jhq0113@163.com
     */
    public $masterConfig;

    /**
     * @var array
     * @example [
     *      'class'     => 'boot\db\Mysql',
     *      'host'      => '127.0.0.1',
     *      'port'      => 3306,
     *      'user'      => 'cock',
     *      'password'  => 'roach',
     *      'database'  => 'cock',
     * ]
     * @datetime 2019/9/17 18:23
     * @author roach
     * @email jhq0113@163.com
     */
    public $slaveConfig;

    /**
     * @var Channel
     * @datetime 2019/9/17 18:25
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_masterPool;

    /**
     * @var Channel
     * @datetime 2019/9/17 18:25
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_slavePool;

    /**
     * @var bool
     * @datetime 2019/9/17 18:29
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_isInit = false;

    /**
     * @param bool $force
     * @datetime 2019/9/17 18:53
     * @author roach
     * @email jhq0113@163.com
     */
    public function initPool($force = false)
    {
        if(!$force && $this->_isInit) {
            return;
        }
        $this->_isInit = true;

        $this->_masterPool = new Channel($this->max);
        $this->_slavePool  = new Channel($this->max);

        for ($index = 0; $index < $this->max; $index++) {
            //创建Master Mysql对象，并未真正连接数据库
            $this->put(true,$this->createConnection(true));
            //创建Slave Mysql对象，并未真正连接数据库
            $this->put(false,$this->createConnection(false));
        }
    }

    /**
     * @param bool $isMaster
     * @return Db
     * @datetime 2019/9/17 18:49
     * @author roach
     * @email jhq0113@163.com
     */
    public function createConnection($isMaster = false)
    {
        return Container::insure($this->slaveConfig,'boot\db\Mysql');
    }

    /**
     * @param bool $isMaster
     * @param Db   $connection
     * @return bool
     * @datetime 2019/9/17 18:50
     * @author roach
     * @email jhq0113@163.com
     */
    public function put($isMaster,Db $connection)
    {
        return $isMaster ? $this->_masterPool->push($connection) : $this->_slavePool->push($connection);
    }

    /**获取数据库连接
     * @param bool $isMaster
     * @return Db
     * @datetime 2019/9/17 18:54
     * @author roach
     * @email jhq0113@163.com
     */
    public function get($isMaster)
    {
        $this->initPool();
        /**
         * @var Db $connection
         */
        return $isMaster ? $this->_masterPool->pop() : $this->_slavePool->pop();
    }

    /**查询
     * @param string  $sql
     * @param array   $params
     * @param bool    $isMaster
     * @return mixed
     * @datetime 2019/9/17 18:56
     * @author roach
     * @email jhq0113@163.com
     */
    public function query($sql, $params = [], $isMaster = false)
    {
        $connection = $this->get($isMaster);
        $result = $connection->query($sql,$params);
        //查询完毕放回连接池
        $this->put($isMaster,$connection);
        return $result;
    }

    /**执行，写操作
     * @param string $sql
     * @param array  $params
     * @return ExecuteResult
     * @datetime 2019/9/17 18:56
     * @author roach
     * @email jhq0113@163.com
     */
    public function execute($sql, $params=[])
    {
        $connection = $this->get(true);
        $result = $connection->execute($sql,$params);
        //执行完毕放回连接池
        $this->put(true,$connection);
        return $result;
    }
}