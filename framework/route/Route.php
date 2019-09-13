<?php
namespace boot\route;

use cockroach\base\Cockroach;
use cockroach\log\Seaslog;
use cockroach\packages\SwooleBoot;

/**
 * Class Route
 * @package boot
 * @datetime 2019/9/12 13:12
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Route extends Cockroach
{
    /**
     * @var SwooleBoot
     * @datetime 2019/9/12 13:16
     * @author roach
     * @email jhq0113@163.com
     */
    public $packager = [];

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
    abstract public function tcp(\Swoole\Server &$server, $fd, $from_id, $data);

    /**
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $from_id
     * @param string         $data
     * @datetime 2019/9/12 13:28
     * @author roach
     * @email jhq0113@163.com
     */
    public function tcpSend(\Swoole\Server &$server,$fd,$from_id,$data)
    {
        $server->send($fd,$data,$from_id);
    }
}