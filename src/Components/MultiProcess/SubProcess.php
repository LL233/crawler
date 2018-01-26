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
    public function handler(\Closure $handle): void
    {
        call_user_func($handle);

        exit(parent::STOP_EXIT);
    }

    /**
     * 进程状态检查
     * 检查信号，查看进程是否处于停止状态
     */
    public function checkProcess(): void
    {
        pcntl_signal_dispatch();

        //停止进程
        if ($this->stopStatus == 1) {
            exit(0);
        }
    }

    /**
     * 子进程的初始化
     *
     * @return void
     */
    private function init(): void
    {
        $this->registerSignalHandler();
    }

    /**
     * 子进程的信号监听
     *
     * @param  int $signal
     * @return void
     */
    public function signalHandler(int $signal): void
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