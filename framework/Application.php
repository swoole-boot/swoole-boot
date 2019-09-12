<?php
namespace boot;

use boot\server\Server;
use boot\server\SwooleBoot;
use cockroach\base\Container;
use cockroach\extensions\ECli;
use Swoole\Runtime;

/**
 * Class Application
 * @package boot
 * @datetime 2019/9/11 12:51
 * @author roach
 * @email jhq0113@163.com
 */
class Application
{
    /**
     * @var Server
     * @datetime 2019/9/11 13:27
     * @author roach
     * @email jhq0113@163.com
     */
    public $server = [];

    /**
     * @var string
     * @datetime 2019/9/11 14:07
     * @author roach
     * @email jhq0113@163.com
     */
    public $host = '0.0.0.0';

    /**
     * @var int
     * @datetime 2019/9/11 14:07
     * @author roach
     * @email jhq0113@163.com
     */
    public $port = 888;

    /**
     * @var int
     * @datetime 2019/9/11 14:08
     * @author roach
     * @email jhq0113@163.com
     */
    public $mode = SWOOLE_PROCESS;

    /**
     * @var int
     * @datetime 2019/9/11 14:08
     * @author roach
     * @email jhq0113@163.com
     */
    public $type = SWOOLE_SOCK_TCP;

    /**
     * @datetime 2019/9/11 14:08
     * @author roach
     * @email jhq0113@163.com
     */
    public function init()
    {
        $serverClass = isset($this->server['class']) ? $this->server['class'] : SwooleBoot::class;
        $serverConfig = $this->server;
        $this->server = new $serverClass($this->host,$this->port,$this->mode,$this->type);

        Container::assem($this->server,$serverConfig);
        unset($serverConfig);

        //服务初始化配置
        $this->server->init();
    }

    /**
     * @var array
     * @datetime 2019/9/11 14:05
     * @author roach
     * @email jhq0113@163.com
     */
    public $cmdMap = [
        'start',
        'stop',
        'restart'
    ];

    /**
     * @datetime 2019/9/11 14:05
     * @author roach
     * @email jhq0113@163.com
     */
    public function handler()
    {
        $params = ECli::params();
        $cmd = $params[0]?:'start';

        if(!in_array($cmd,$this->cmdMap)) {
            ECli::error('服务仅支持:'.implode(',',$this->cmdMap).'命令');
            exit();
        }

        call_user_func([$this,$cmd]);
    }

    /**
     * @datetime 2019/9/11 13:45
     * @author roach
     * @email jhq0113@163.com
     */
    public function start()
    {
        $status = $this->server->beforeStart();
        if(!$status) {
            return;
        }

        /**
         * 一键协程
         */
        Runtime::enableCoroutine(true);

        ECli::info('starting');

        $this->server->start();
    }

    /**
     * @datetime 2019/9/11 13:42
     * @author roach
     * @email jhq0113@163.com
     */
    public function stop()
    {
        $status = $this->server->forceStop();
        if($status) {
            ECli::info('stop success');
        } else {
            ECli::error('stop failed or timeout!');
        }
    }

    /**
     * @datetime 2019/9/11 13:39
     * @author roach
     * @email jhq0113@163.com
     */
    public function restart()
    {
        $this->stop();
        $this->start();
    }
}