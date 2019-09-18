<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/9/18
 * Time: 10:01 PM
 */

namespace boot;

use cockroach\base\Extension;
use Swoole\Coroutine;

/**
 * Class Context
 * @package boot
 * @datetime 2019/9/18 10:01 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Context extends Extension
{
    /**
     * @var \ArrayObject
     * @datetime 2019/9/18 10:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected static $_pool;

    /**
     * @datetime 2019/9/18 11:20 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static protected function _initPool()
    {
        if(!isset(static::$_pool)) {
            static::$_pool = new \ArrayObject();
        }
    }

    /**
     * @return bool
     * @datetime 2019/9/18 10:33 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function inCoroutine()
    {
        return Coroutine::getCid() > 0;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @datetime 2019/9/18 10:36 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function set($key, $value)
    {
        if(static::inCoroutine()) {
            Coroutine::getContext()[ $key ] = $value;
        } else {
            static::_initPool();
            static::$_pool[ $key ] = $value;
        }
    }

    /**
     * @param string $key
     * @param null   $cid
     * @return mixed
     * @datetime 2019/9/18 10:46 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function get($key, $cid = null)
    {
        if(!is_null($cid)) {
            return Coroutine::getContext($cid)[ $key ] ?? null;
        }

        if(static::inCoroutine()) {
            return Coroutine::getContext()[ $key ] ?? null;
        }

        return static::$_pool[ $key ] ?? null;
    }

    /**
     * @param string $key
     * @param null   $cid
     * @return bool
     * @datetime 2019/9/18 10:54 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function exists($key, $cid = null)
    {
        if(!is_null($cid)) {
            return isset(Coroutine::getContext($cid)[ $key ]);
        }

        if(static::inCoroutine()) {
            return isset(Coroutine::getContext()[ $key ]);
        }

        return isset(static::$_pool[ $key ]);
    }

    /**
     * @param string $key
     * @param null   $cid
     * @datetime 2019/9/18 11:00 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function delete($key, $cid = null)
    {
        if(!is_null($cid)) {
            unset(Coroutine::getContext($cid)[ $key ]);
            return;
        }

        if(static::inCoroutine()) {
            unset(Coroutine::getContext()[ $key ]);
        }else {
            unset(static::$_pool[ $key ]);
        }
    }

    /**
     * @param Coroutine\Context $context
     * @datetime 2019/9/18 11:17 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static protected function _unsetContext(Coroutine\Context $context)
    {
        foreach ($context as $index => $value) {
            $context->offsetUnset($index);
        }
    }

    /**
     * @param int $cid
     * @datetime 2019/9/18 11:18 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function flush($cid = null)
    {
        if(!is_null($cid)) {
            static::_unsetContext(Coroutine::getContext($cid));
            return;
        }

        if(static::inCoroutine()) {
            static::_unsetContext(Coroutine::getContext());
        }else {
            static::$_pool = null;
        }
    }
}