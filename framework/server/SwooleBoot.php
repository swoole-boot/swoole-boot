<?php
namespace boot\server;

use boot\Func;

/**
 * Class SwooleBoot
 * @package boot\server
 * @datetime 2019/9/11 12:56
 * @author roach
 * @email jhq0113@163.com
 */
class SwooleBoot extends Server
{
    /**
     * @var array
     * @datetime 2019/9/11 13:20
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_defaultSettings = [
        'daemonize'             => true,
        'log_file'              => '/tmp/swoole-boot.error.log',
        'open_length_check'     => true,
        'package_max_length'    => 81920,
        'package_length_type'   => 'N',
        'package_length_offset' => 0,
        //包头长度为N字节，length的值不包含包头，仅包含包体，package_body_offset设置为N
        'package_body_offset'   => 69,
    ];

    /**绑定事件
     * @return mixed|void
     * @datetime 2019/9/11 13:16
     * @author roach
     * @email jhq0113@163.com
     */
    public function bindEvent()
    {
        $this->on('start' , [ $this, 'onStart']);
        $this->on('shutdown' , [ $this, 'onStop']);
        $this->on('receive' , [ $this, 'onReceive']);
        $this->on('WorkerError' , [ $this,'onWorkerError']);
        $this->on('WorkerStart' , [ $this,'onWorkerStart']);
        $this->on('WorkerExit' , [ $this,'onWorkerExit']);
    }

    /**
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $from_id
     * @param string         $data
     * @datetime 2019/9/11 13:19
     * @author roach
     * @email jhq0113@163.com
     */
    public function onReceive(\Swoole\Server $server, $fd, $from_id, $data)
    {
        //解包
         $package = $this->dispatcher->tcp($server,$fd,$from_id,$data);
         if($package) {
             //路由
             $func = $this->router->route($package);
             if(!($func instanceof Func)) {
                 return $this->dispatcher->tcpSend($server, $fd, $from_id, $func, $package);
             }

             $func->server = $server;
             $func->logger = $server->logger;

            $this->dispatcher->dispatcher($func, function($return) use ($server, $fd, $from_id, $package){
                 return $this->dispatcher->tcpSend($server, $fd, $from_id, $return, $package);
             });
         }
    }
}