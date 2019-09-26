<?php
namespace boot\extensions;

use cockroach\base\Extension;

/**
 * Class ESwoole
 * @package boot\extensions
 * @datetime 2019/9/26 17:50
 * @author roach
 * @email jhq0113@163.com
 */
class ESwoole extends Extension
{
    /**
     * @var array
     * @datetime 2019/9/26 17:52
     * @author roach
     * @email jhq0113@163.com
     */
    private static $_ips;

    /**获取lan ip
     * @param string $netInterfaceName  网卡名字
     * @param string $defaultValue      默认值
     * @return mixed|string
     * @datetime 2019/9/26 17:55
     * @author roach
     * @email jhq0113@163.com
     */
    static public function getLanIp($netInterfaceName = 'eth0', $defaultValue = '')
    {
        if(!isset(self::$_ips)) {
            self::$_ips = swoole_get_local_ip();
        }

        return self::$_ips[ $netInterfaceName ] ?? $defaultValue;
    }
}