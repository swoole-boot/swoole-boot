<?php
namespace boot\db;

use boot\Application;
use cockroach\base\Cockroach;
use cockroach\log\ILog;

/**
 * Class Db
 * @package boot\db
 * @datetime 2019/9/17 18:31
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Db extends Cockroach
{
    /**
     * @datetime 2019/9/17 18:08
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_client;

    /**
     * @var ILog
     * @datetime 2019/9/19 18:57
     * @author roach
     * @email jhq0113@163.com
     */
    public $logger = [];

    /**
     * @param array $config
     * @datetime 2019/9/19 18:57
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        if(!isset($config['logger'])) {
            $this->logger = Application::$app->server->logger;
        }

        parent::init($config);
    }

    /**
     * @datetime 2019/9/17 18:08
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _client()
    {
        if($this->_client instanceof \Swoole\Coroutine\Mysql) {
            return $this->_client;
        }

        return $this->connect();
    }

    /**
     * @return mixed
     * @datetime 2019/9/17 18:43
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function connect();

    /**
     * @param string $sql
     * @param array  $params
     * @return mixed
     * @datetime 2019/9/17 18:31
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function query($sql,$params=[]);

    /**
     * @param string $sql
     * @param array  $params
     * @return ExecuteResult
     * @datetime 2019/9/17 18:32
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function execute($sql,$params=[]);
}