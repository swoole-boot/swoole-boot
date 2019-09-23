<?php
namespace boot\dispatcher;

use cockroach\extensions\EReturn;

/**
 * Class SwooleBoot
 * @package boot\dispatcher
 * @datetime 2019/9/23 11:13
 * @author roach
 * @email jhq0113@163.com
 */
class SwooleBoot extends Dispatcher
{
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
    public function tcp(\Swoole\Server $server, $fd, $from_id, $data)
    {
        /**
         * @var \boot\server\SwooleBoot $server
         */
        $server->logger->info('receive data:' . base64_encode($data));

        //解包
        $package = $this->packager->unpack($data);

        $server->logger->info(json_encode($package, JSON_UNESCAPED_UNICODE));

        //校验包协议
        if (!isset($package['header'], $package['data']['func'], $package['data']['params'])) {
            $this->tcpSend($server, $fd, $from_id, $this->healthResponse);
            return false;
        }

        //校验请求id
        if (!isset($package['data']['params']['requestId'])) {
            $packData = $this->packager->packBySerializeId(EReturn::error('requestId is required'), $package['header']['Serialize']);
            $this->tcpSend($server, $fd, $from_id, $packData);
            return false;
        }

        //设置请求id
        $server->logger->setRequestId($package['data']['params']['requestId']);

        //校验请求ip
        if (!isset($package['data']['params']['clientIp'])) {
            $packData = $this->packager->packBySerializeId(EReturn::error('clientIp is required'), $package['header']['Serialize']);
            $this->tcpSend($server, $fd, $from_id, $packData);
            return false;
        }

        return $package;
    }
}