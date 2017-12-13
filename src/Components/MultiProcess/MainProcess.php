<?php

namespace Crawler\Components\MultiProcess;

use Crawler\Components\SignalHandler\SignalHandler;

/**
 * 主进程
 * 负责启动，停止，回收子进程
 * 主要起到一个管理器的作用，并不做其它的逻辑处理
 *
 * @author LL
 */
class MainProcess
{
    /**
     * 信号监听处理
     * @var SignalHandler
     */
    private $signalHndler;

    /**
     * 主进程构造函数
     * 设置信号监听
     *
     * @param SignalHandler $signalHandler
     */
    public function __construct(SignalHandler $signalHandler)
    {
        $this->signalHndler = $signalHandler;

        declare(ticks = 1);

        register_tick_function([$this, 'registerSignalHandler']);
    }

    /**
     * 设置信号监听
     *
     * @return void
     */
    public function registerSignalHandler()
    {
        pcntl_signal(SIGTERM, [$this->signalHndler, 'sigHandle']);
        pcntl_signal(SIGINT, [$this->signalHndler, 'sigHandle']);
        pcntl_signal(SIGHUP, [$this->signalHndler, 'sigHandle']);
        pcntl_signal(SIGQUIT, [$this->signalHndler, 'sigHandle']);
        pcntl_signal(SIGUSR1, [$this->signalHndler, 'sigHandle']);
    }
}