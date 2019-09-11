<?php
namespace boot\server;

use boot\Application;
use cockroach\base\Container;
use cockroach\extensions\ECli;
use cockroach\extensions\EFile;

/**
 * Class Server
 * @package boot\server
 * @datetime 2019/9/11 12:55
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Server extends \Swoole\Server
{
    /**
     * @var Application
     * @datetime 2019/9/11 12:57
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_app;

    /**
     * @param Application $app
     * @datetime 2019/9/11 13:06
     * @author roach
     * @email jhq0113@163.com
     */
    public function setApp(Application $app)
    {
        $this->_app = $app;
    }

    /**
     * @return Application
     * @datetime 2019/9/11 13:07
     * @author roach
     * @email jhq0113@163.com
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @var array
     * @datetime 2019/9/11 13:20
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_defaultSettings = [
        'daemonize'             => true,
        'log_file'              => '/tmp/swoole-boot.error.log'
    ];

    /**
     * @datetime 2019/9/11 14:14
     * @author roach
     * @email jhq0113@163.com
     */
    public function init()
    {
        $this->setting = array_merge($this->_defaultSettings,$this->setting);
        //$this->set($this->setting);
    }

    /**绑定事件
     * @return mixed
     * @datetime 2019/9/11 13:16
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function bindEvent();

    //region 1.Server相关
    /**
     * @var string
     * @datetime 2019/9/11 13:08
     * @author roach
     * @email jhq0113@163.com
     */
    public $pidFile = '/var/swoole-boot.pid';

    /**
     * @param \Swoole\Server $server
     * @datetime 2019/9/11 13:13
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _createPid(\Swoole\Server $server)
    {
        EFile::mkdir(dirname($this->pidFile),0775);
        EFile::write($this->pidFile,$server->master_pid,false);
    }

    /**
     * @datetime 2019/9/11 13:13
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _deletePid()
    {
        @unlink($this->pidFile);
    }

    /**获取pid值
     * @return string
     * @datetime 2019/9/11 13:31
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _getPid()
    {
        return EFile::read($this->pidFile);
    }

    /**
     * @var int
     * @datetime 2019/9/11 13:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $forceStopTimeout = 10;

    /**
     * @return bool
     * @datetime 2019/9/11 13:44
     * @author roach
     * @email jhq0113@163.com
     */
    public function beforeStart()
    {
        if(!empty($this->_getPid())) {
            ECli::error('server has started!');
            return false;
        }

        return true;
    }

    /**
     * @param \Swoole\Server $server
     * @datetime 2019/9/11 13:02
     * @author roach
     * @email jhq0113@163.com
     */
    public function onStart(\Swoole\Server $server)
    {
        $this->_createPid($server);
    }

    /**手动停止服务
     * @datetime 2019/9/11 13:38
     * @author roach
     * @email jhq0113@163.com
     */
    public function forceStop()
    {
        $pid = $this->_getPid();
        if(empty($pid)) {
            ECli::error('stop failed:未找到pid');
            return false;
        }

        `/bin/kill -s SIGTERM {$pid}`;

        $time = time();
        while(true){
            $current = time();

            if(!file_exists($this->pidFile)) {
                return true;
            }

            if($current - $time > $this->forceStopTimeout) {
                return false;
            }

            usleep(100);
        }

        return true;
    }

    /**
     * @param \Swoole\Server $server
     * @datetime 2019/9/11 13:04
     * @author roach
     * @email jhq0113@163.com
     */
    public function onStop(\Swoole\Server $server)
    {
        $this->_deletePid();
    }
    //endregion

    //region 2.Worker相关
    /**
     * @var array
     * @datetime 2019/9/11 12:58
     * @author roach
     * @email jhq0113@163.com
     */
    public $components = [];

    /**
     * @datetime 2019/9/11 12:59
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _initComponents()
    {
        foreach ($this->components as $key => $component) {
            Container::set($key,$component);
        }
    }

    /**
     * @param \Swoole\Server $server
     * @param $workerId
     * @datetime 2019/9/11 13:02
     * @author roach
     * @email jhq0113@163.com
     */
    public function onWorkerStart(\Swoole\Server $server, $workerId)
    {
        //初始化组件
        $this->_initComponents();
    }

    /**
     * @param \Swoole\Server $sever
     * @param $workerId
     * @datetime 2019/9/11 13:01
     * @author roach
     * @email jhq0113@163.com
     */
    public function onWorkerExit(\Swoole\Server $sever, $workerId)
    {

    }

    /**
     * @param \Swoole\Server $server
     * @param $workerId
     * @param $workerPid
     * @param $exitCode
     * @param $signal
     * @datetime 2019/9/11 13:01
     * @author roach
     * @email jhq0113@163.com
     */
    public function onWorkerError(\Swoole\Server $server, $workerId, $workerPid, $exitCode, $signal)
    {
    }
    //endregion
}