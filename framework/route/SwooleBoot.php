<?php
namespace boot\route;

use boot\Application;
use boot\Func;
use boot\server\Server;
use cockroach\base\Container;
use cockroach\extensions\EReturn;

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
     * @param array $package
     * @return  Func | array
     * @datetime 2019/9/23 13:20
     * @author roach
     * @email jhq0113@163.com
     */
    public function route(array $package)
    {
        $funcClass = $this->funcNamespace.'\\'.str_replace($this->separator,'\\',$package['data']['func']);
        $position = mb_strrpos($funcClass,'\\');
        if($position < mb_strlen($funcClass)) {
            $funcClass = mb_substr($funcClass,0,$position + 1).ucfirst(mb_substr($funcClass,$position + 1));
        }

        if(!class_exists($funcClass)) {
            Application::$app->server->logger->warning('class [{class}]不存在',[
                'class' => $funcClass
            ]);

            return EReturn::error(EReturn::ERROR_PARAMS,'func ['.$package['data']['func'].']不存在');
        }

        return Container::insure([
            'class'    => $funcClass,
            'params'    => $package['data']['params'],
            'requestId' => $package['data']['params']['requestId'],
            'clientIp'  => $package['data']['params']['clientIp']
        ]);
    }
}