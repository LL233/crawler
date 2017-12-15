<?php

namespace Crawler\Components\MultiProcess;

/**
 * 子进程
 * 负责处理具体的业务逻辑
 *
 * @author LL
 */
class SubProcess extends BaseProcess
{
    public function __construct()
    {
        $this->init();
    }

    /**
     * 子进程的执行方法
     *
     * @param  \Closure $handle
     * @return void
     */
    public function handler(\Closure $handle)
    {

    }

    /**
     * 子进程的初始化
     *
     * @return void
     */
    private function init()
    {
        $this->registerSignalHandler();
    }

    protected function signalHandler($signal)
    {

    }
}