<?php
namespace boot\dispatcher;

use boot\Application;
use boot\extensions\ESwoole;
use boot\Func;
use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\consul\Client;
use cockroach\consul\Service;
use cockroach\extensions\EReturn;
use cockroach\extensions\EValidate;
use cockroach\packages\SwooleBoot;

/**
 * Class Dispatcher
 * @package boot
 * @datetime 2019/9/23 10:46
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Dispatcher extends Cockroach
{
    /**
     * @var Client
     * @datetime 2019/9/23 15:12
     * @author roach
     * @email jhq0113@163.com
     */
    public $register = [];

    /**
     * @var string
     * @datetime 2019/9/23 15:41
     * @author roach
     * @email jhq0113@163.com
     */
    public $registerName;

    /**
     * @var string
     * @datetime 2019/9/23 15:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $registerHost;

    /**
     * @var int
     * @datetime 2019/9/23 15:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $registerPort;

    /**
     * @var string
     * @datetime 2019/9/23 15:38
     * @author roach
     * @email jhq0113@163.com
     */
    public $registerNode;

    /**
     * @var SwooleBoot
     * @datetime 2019/9/12 13:16
     * @author roach
     * @email jhq0113@163.com
     */
    public $packager = [];

    /**
     * @var string
     * @datetime 2019/9/23 11:23
     * @author roach
     * @email jhq0113@163.com
     */
    public $healthResponse = 'ok';

    /**
     * @var string
     * @datetime 2019/9/23 13:42
     * @author roach
     * @email jhq0113@163.com
     */
    public $errorHandler = 'boot\Error';

    /**
     * @param \Swoole\Server    $server
     * @param int               $fd
     * @param int               $from_id
     * @param string            $data
     * @return mixed
     * @datetime 2019/9/12 13:22
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function tcp(\Swoole\Server $server, $fd, $from_id, $data);

    /**
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $from_id
     * @param string         $data
     * @param array         $package
     * @datetime 2019/9/12 13:28
     * @author roach
     * @email jhq0113@163.com
     */
    public function tcpSend(\Swoole\Server $server, $fd, $from_id, $data, $package = null)
    {
        $data = !isset($package['header']['Serialize']) ? $data : $this->packager->packBySerializeId($data,$package['header']['Serialize']);
        $server->send($fd,$data,$from_id);
    }

    /**
     * @datetime 2019/9/23 15:41
     * @author roach
     * @email jhq0113@163.com
     */
    public function registerService()
    {
        $serviceMeta = Application::$app->server->router->getFuncs();
        //consul的metaData的value长度不能超过512
        array_map(function ($value){
            if(strlen($value) > 512) {
                return substr($value,0,512);
            }
        }, $serviceMeta);
        //规定协议
        $serviceMeta['protocol'] = 'swoole-boot';

        $this->registerHost = $this->registerHost ?? ESwoole::getLanIp();
        $this->registerHost = empty($this->registerHost) ? Application::$app->host : $this->registerHost;
        $this->registerPort = $this->registerPort ?? Application::$app->port;
        $this->registerNode = $this->registerNode ?? $this->registerHost;
        $this->registerName = $this->registerName ?? Application::$app->name;

        /**
         * @var Service $service
         */
        $service = Container::insure([
            'class'          => 'cockroach\consul\Service',
            'dataCenter'     => $this->register->dataCenter,
            'serviceName'    => $this->registerName,
            'node'           => $this->registerNode,
            'address'        => $this->registerHost,
            'serviceAddress' => $this->registerHost,
            'servicePort'    => $this->registerPort,
            'serviceMeta'    => $serviceMeta,
            'lan'            => $this->registerHost,
            'wan'            => $this->registerHost,
            'check'          => [
                    'CheckID'   => $this->registerHost."_".$this->registerPort.'_port',
                    'Name'      => $this->registerName,
                    'Notes'     => 'swoole-boot服务端口检测',
                    //'Status'    => 'passing',
                    'Definition' => [
                        'TCP' => $this->registerHost.':'.$this->registerPort,
                        'Interval' => '3s',
                        'Timeout'  => '1s',
                        'DeregisterCriticalServiceAfter' => '12s'
                    ]
                ]
        ]);

        $classes = class_implements('boot/Func');
        Application::$app->server->logger->error(json_encode($service->toArray()));
        //\cockroach\extensions\EFile::write('/tmp/class.log',json_encode($service->toArray()).PHP_EOL);
        //$this->register->register($service);
    }

    /**
     * @param Func $func
     * @param callable $callback
     * @return mixed
     * @datetime 2019/9/23 13:35
     * @author roach
     * @email jhq0113@163.com
     */
    public function dispatcher(Func $func, callable $callback)
    {
        try {
            $return = $func->beforeRun();
            if (!EReturn::success($return)) {
                return call_user_func($callback, $return);
            }

            //增加数据校验
            $rules = $func->rules();
            if (!empty($rules)) {
                $result = EValidate::rules($rules, $func->params);
                if (!empty($result)) {
                    return call_user_func($callback, EReturn::error(EReturn::ERROR_PARAMS, '参数错误', $result));
                }
            }

            //执行func
            $data = $func->run();

            //响应结果
            if(!empty($data)) {
                $result = $func->afterRun($data);
                return call_user_func($callback, $result);
            }
        } catch (\Throwable $throwable) {
            /**
             * @var Func $errorHandler
             */
            $errorHandler = Container::insure([
                'class'     => $this->errorHandler,
                'throwable' => $throwable
            ]);

            $errorHandler->run();
        }
    }
}