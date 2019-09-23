<?php

namespace boot;

/**
 * Class Error
 * @package boot
 * @datetime 2019/9/23 13:46
 * @author roach
 * @email jhq0113@163.com
 */
class Error extends Func
{
    /**
     * @var \Throwable
     * @datetime 2019/9/23 13:47
     * @author roach
     * @email jhq0113@163.com
     */
    public $throwable;

    /**
     * @return mixed|void
     * @datetime 2019/9/23 13:49
     * @author roach
     * @email jhq0113@163.com
     */
    public function run()
    {
        $this->logger->error('file:[{file}][{line}]行出现错误，message:{msg}',[
            'file' => $this->throwable->getFile(),
            'line' => $this->throwable->getLine(),
            'msg'  => $this->throwable->getMessage(),
        ]);
    }
}