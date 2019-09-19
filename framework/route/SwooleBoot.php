<?php
namespace boot\route;

use boot\Func;
use boot\server\Server;
use cockroach\base\Container;
use cockroach\extensions\EReturn;
use cockroach\extensions\EValidate;

/**
 * Class SwooleBoot
 * @package boot\route
 * @datetime 2019/9/12 13:21
 * @author roach
 * @email jhq0113@163.com
 */
class SwooleBoot extends Route
{
    /**
     * @var string
     * @datetime 2019/9/12 13:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $funcNamespace = 'app\funcs';

    /**
     * @var string
     * @datetime 2019/9/12 13:29
     * @author roach
     * @email jhq0113@163.com
     */
    public $healthResponse = 'ok';

    /**内部请求
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $from_id
     * @param string         $data
     * @datetime 2019/9/12 13:29
     * @author roach
     * @email jhq0113@163.com
     */
    public function innerHandler(\Swoole\Server $server, $fd, $from_id, $data)
    {
        /**
         * @var Func $func
         */
        $func = Container::createCockroach([
            'class'     => $this->funcNamespace.'\Inner'
        ]);

        $return = $func->beforeRun();
        if(!EReturn::isSuccess($return)) {
            return $this->tcpSend($server,$fd,$from_id,$return['message']);
        }

        $data = $func->run();

        $result = $func->afterRun($data);

        return $this->tcpSend($server,$fd,$from_id,$result);
    }

    /**tcp请求路由
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $from_id
     * @param                $data
     * @return mixed|void
     * @datetime 2019/9/12 13:23
     * @author roach
     * @email jhq0113@163.com
     */
    public function tcp(\Swoole\Server &$server, $fd, $from_id, $data)
    {
        /**
         * @var \boot\server\SwooleBoot $server
         */
        $server->logger->info('receive data:'.base64_encode($data));

        //包长度小于协议包头大小,属于内部请求
        if(strlen($data) < $this->packager->headerSize) {
            return $this->innerHandler($server,$fd,$from_id,$data);
        }

        //解包
        $package = $this->packager->unpack($data);

        $server->logger->info(json_encode($package,JSON_UNESCAPED_UNICODE));

        //校验包协议
        if(!isset($package['header'], $package['data']['func'], $package['data']['params'])) {
            return $this->tcpSend($server,$fd,$from_id,$this->healthResponse);
        }

        //校验请求id
        if(!isset($package['data']['params']['requestId'])) {
            $packData = $this->packager->packBySerializeId(EReturn::error('requestId is required'),$package['header']['Serialize']);
            return $this->tcpSend($server,$fd,$from_id,$packData);
        }

        //设置请求id
        $server->logger->setRequestId($package['data']['params']['requestId']);

        //校验请求ip
        if(!isset($package['data']['params']['clientIp'])) {
            $packData = $this->packager->packBySerializeId(EReturn::error('clientIp is required'),$package['header']['Serialize']);
            return $this->tcpSend($server,$fd,$from_id,$packData);
        }

        if($package['data']['func'] == 'Inner') {
            //按照请求包的序列化方式封包
            $packData = $this->packager->packBySerializeId(EReturn::error('forbidden'),$package['header']['Serialize']);
            return $this->tcpSend($server,$fd,$from_id,$packData);
        }

        //路由请求
        $this->route($server,$package['data'], function ($response) use(&$server,$fd,$from_id,$package){
            //按照请求包的序列化方式封包
            $packData = $this->packager->packBySerializeId($response,$package['header']['Serialize']);
            return $this->tcpSend($server,$fd,$from_id,$packData);
        });
    }

    /**
     * @param Server   $server
     * @param mixed    $data
     * @param callable $callback
     * @return mixed
     * @datetime 2019/9/13 14:54
     * @author roach
     * @email jhq0113@163.com
     */
    public function route(&$server,$data,$callback)
    {
        //支持目录
        $func = $this->funcNamespace.'\\'.$data['func'];
        if(!class_exists($func)) {
            return call_user_func($callback, EReturn::error('method not exists'));
        }

        /**
         * @var Func $func
         */
        $func = Container::createCockroach([
            'class'     => $func,
            'params'    => $data['params'],
            'requestId' => $data['params']['requestId'],
            'clientIp'  => $data['params']['clientIp'],
            'logger'    => $server->logger,
            'server'    => $server
        ]);

        $return = $func->beforeRun();
        if(!EReturn::success($return)) {
            return call_user_func($callback,$return);
        }

        //增加数据校验
        $rules = $func->rules();
        if(!empty($rules)) {
            $result = EValidate::rules($rules,$data['params']);
            if(!empty($result)) {
                return call_user_func($callback,EReturn::error(EReturn::ERROR_PARAMS,'参数错误',$result));
            }
        }

        $data = $func->run();

        $result = $func->afterRun($data);

        return call_user_func($callback,$result);
    }
}