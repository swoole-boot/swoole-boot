<?php
namespace boot;

use boot\server\Server;
use boot\server\SwooleBoot;
use cockroach\base\Container;
use cockroach\events\Event;
use cockroach\extensions\ECli;
use cockroach\extensions\EFile;
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
    use Event;

    /**
     * 服务启动
     */
    const EVENT_SERVER_START = 'app:server:start';

    /**
     * 服务停止
     */
    const EVENT_SERVER_STOP = 'app:server:stop';

    /**
     * 工作进程启动
     */
    const EVENT_WORKER_START = 'app:worker:start';

    /**
     * 工作进程退出
     */
    const EVENT_WORKER_EXIT = 'app:worker:exit';

    /**
     * 工作进程出现错误
     */
    const EVENT_WORKER_ERROR = 'app:worker:error';

    /**
     * @var Server
     * @datetime 2019/9/11 13:27
     * @author roach
     * @email jhq0113@163.com
     */
    public $server = [];

    /**
     * @var int
     * @datetime 2019/9/13 14:11
     * @author roach
     * @email jhq0113@163.com
     */
    public $stopTimeout = 6;

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
        $this->server['class'] = isset($this->server['class']) ? $this->server['class'] : SwooleBoot::class;
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
        $serverClass = $this->server['class'];
        $serverConfig = $this->server;

        //创建服务对象
        $this->server = new $serverClass($this->host,$this->port,$this->mode,$this->type);
        $this->server->setApp($this);
        Container::assem($this->server,$serverConfig);
        unset($serverConfig);

        //服务初始化配置
        $this->server->init();
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
        $pid = EFile::read($this->server['pidFile']);
        if(empty($pid)) {
            ECli::error('stop failed:未找到pid');
            exit();
        }

        `/bin/kill -s SIGTERM {$pid}`;

        $time = time();
        while(true){
            $current = time();

            if(!file_exists($this->server['pidFile'])) {
                ECli::info('stop success');
                break;
            }

            if($current - $time > $this->stopTimeout) {
                ECli::error('stop failed or timeout!');
                break;
            }

            usleep(100);
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