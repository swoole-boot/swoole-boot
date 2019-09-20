<?php
namespace boot\db;

use cockroach\base\Container;
use cockroach\log\ILog;
use Swoole\Coroutine\MySQL\Statement;

/**
 * Class Mysql
 * @package boot\db
 * @datetime 2019/9/17 18:05
 * @author roach
 * @email jhq0113@163.com
 */
class Mysql extends Db
{
    /**
     * @var string
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $host;

    /**
     * @var int
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $port = 3306;

    /**
     * @var int
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $timeout = 3;

    /**
     * @var string
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $user;

    /**
     * @var string
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $password;

    /**
     * @var string
     * @datetime 2019/9/17 18:06
     * @author roach
     * @email jhq0113@163.com
     */
    public $database;

    /**
     * @var string
     * @datetime 2019/9/17 18:07
     * @author roach
     * @email jhq0113@163.com
     */
    public $charset = 'utf8';

    /**
     * @var ILog
     * @datetime 2019/9/17 18:07
     * @author roach
     * @email jhq0113@163.com
     */
    public $logger = [];

    /**
     * @return \Swoole\Coroutine\MySQL|null
     * @datetime 2019/9/17 18:10
     * @author roach
     * @email jhq0113@163.com
     */
    public function connect()
    {
        $this->_client = new \Swoole\Coroutine\MySQL();

        $this->logger->info('begin connect mysql [{host}:{port}][db:{db}][user:{user}]',[
            'host' => $this->host,
            'port' => $this->port,
            'db'   => $this->database,
            'user' => $this->user
        ]);

        $isConnect = $this->_client->connect([
            'host'      => $this->host,
            'user'      => $this->user,
            'password'  => $this->password,
            'database'  => $this->database,
            'charset'   => $this->charset,
            'port'      => $this->port,
            'timeout'   => $this->timeout
        ]);

        if(!$isConnect) {
            $this->_client = null;
            $this->logger->error('[{host}:{port}][db:{db}][user:{user}] [mysql connect failed]',[
                'host'      => $this->host,
                'port'      => $this->port,
                'db'        => $this->database,
                'user'      => $this->user
            ]);
        }

        return $this->_client;
    }

    /**查询
     * @param string $sql
     * @param array  $params
     * @return array
     * @datetime 2019/9/17 18:16
     * @author roach
     * @email jhq0113@163.com
     */
    public function query($sql,$params=[])
    {
        $statement = $this->_statement($sql);
        if(!$statement) {
            return [];
        }

        $this->logger->debug('sql:{sql},params:{params}',[
            'sql'     => $sql,
            'params'  => json_encode($params,JSON_UNESCAPED_UNICODE)
        ]);

        return $statement->execute($params);
    }

    /**执行，写操作
     * @param string $sql
     * @param array  $params
     * @return ExecuteResult
     * @datetime 2019/9/17 18:18
     * @author roach
     * @email jhq0113@163.com
     */
    public function execute($sql,$params=[])
    {
        /**
         * @var Statement $statement
         */
        $statement = $this->_statement($sql);
        if(!$statement) {
            return null;
        }

        $statement->execute($params);

        $this->logger->debug('sql:{sql},params:{params}',[
            'sql'     => $sql,
            'params'  => json_encode($params,JSON_UNESCAPED_UNICODE)
        ]);

        return Container::insure([
            'class'        => 'boot\db\ExecuteResult',
            'affectedRows' => $statement->affected_rows,
            'lastInsertId' => $statement->insert_id,
            'error'        => $statement->error,
            'errno'        => $statement->errno
        ]);
    }

    /**
     * @param string $sql
     * @return mixed
     * @datetime 2019/9/17 18:13
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _statement($sql)
    {
        $statement = $this->_client()->prepare($sql);

        //断线重连一次
        if(!$statement) {
            $this->logger->error('prepare [{sql}]语句的时候发生错误，[{errno}],[{error}]',[
                'sql'   => $sql,
                'errno' => $this->_client->errno,
                'error' => $this->_client->error
            ]);

            $this->_client = null;
            $statement = $this->_client()->prepare($sql);
        }

        return $statement;
    }
}