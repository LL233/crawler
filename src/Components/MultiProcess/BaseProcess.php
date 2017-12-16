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
        pcntl_signal(SIGTERM, [$this, "signalHandler"]);
        pcntl_signal(SIGINT, [$this, "signalHandler"]);
        pcntl_signal(SIGUSR1, [$this, "signalHandler"]);
    }

    /**
     * 获取保存pid文件的路径
     *
     * @return string
     */
    protected function getSavePidPath()
    {
        return '/var/run/crawler.pid';
    }

    /**
     * 保存pid
     *
     * @return void
     */
    protected function savePid()
    {
        $pid = posix_getpid();

        file_put_contents($pid, $this->getSavePidPath());
    }
}