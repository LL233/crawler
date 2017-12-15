<?php

namespace Crawler\Components\MultiProcess;

/**
 * 进程基类
 * 提供进程中信号的注册监听方法
 *
 * @author LL
 */
abstract class BaseProcess
{
    /**
     * 信号处理
     *
     * @param  int $signal
     * @return void
     */
    abstract protected function signalHandler($signal);

    /**
     * 注册信号监听
     *
     * @return void
     */
    protected function registerSignalHandler()
    {
        pcntl_signal(SIGTERM, [$this, "sigHandle"]);
        pcntl_signal(SIGINT, [$this, "sigHandle"]);
        pcntl_signal(SIGHUP, [$this, "sigHandle"]);
        pcntl_signal(SIGQUIT, [$this, "sigHandle"]);
        pcntl_signal(SIGUSR1, [$this, "sigHandle"]);
    }
}