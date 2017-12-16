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
    /**
     * 子进程每次执行完休眠时间
     *
     * @var float
     */
    private $sleepTime = 0.01;

    /**
     * 子进程的停止状态
     *
     * @var int
     */
    private $stopStatus = 0;

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
        while (true) {
            call_user_func($handle);

            //停止进程
            if ($this->stopStatus == 1) {
                exit(0);
            }

            sleep($this->sleepTime);
        }
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

    /**
     * 子进程的信号监听
     *
     * @param  int $signal
     * @return void
     */
    protected function signalHandler($signal)
    {
        switch ($signal) {
            //停止
            case SIGTERM :
            case SIGINT :
                $this->stopStatus = 1;
                break;
        }
    }
}