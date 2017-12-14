<?php

namespace Crawler\Components\MultiProcess;

use Crawler\Components\SignalHandler\SignalHandler;
use Exception;

/**
 * 主进程
 * 负责启动，停止，回收子进程，守护进程化
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

    /**
     * 使进程变为守护进程
     *
     * @author LL
     */
    public function daemonize()
    {
        //只有在cli模式下可以变为守护进程
        if (php_sapi_name() != 'cli') {
            return false;
        }

        //文件掩码清0
        umask(0);

        //第一次生成子进程
        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new Exception('fork fail in daemonize exe');
        } elseif ($pid > 0) {
            //父进程退出
            exit(0);
        }

        //设置为新会话组长，脱离终端
        if (posix_setsid() < 0) {
            throw new Exception('setsid fail in daemonize exe');
        }

        //第二次生成子进程
        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new Exception('second time fork fail in daemonize exe');
        } elseif ($pid > 0) {
            //父进程退出
            exit(0);
        }
    }
}