<?php

namespace Crawler\Components\MultiProcess;

use Exception;

/**
 * 主进程
 * 负责启动，停止，回收子进程，守护进程化
 * 主要起到一个管理器的作用，并不做其它的逻辑处理
 *
 * @author LL
 */
class MainProcess extends BaseProcess
{
    /**
     * 保存子进程的进程id
     *
     * @var array
     */
    private $subProcessPidMap = [];

    /**
     * 子进程的最大数量
     *
     * @var int
     */
    private $subProcessMaxCount;

    /**
     * 子进程的执行事件
     *
     * @var \Closure
     */
    private $subProcessHandle;

    /**
     * 进程休眠时间
     *
     * @var float
     */
    private $sleepTime = 0.1;

    /**
     * 主进程构造函数
     * 设置信号监听
     *
     * @param \Closure      $handle         子进程的执行事件
     */
    public function __construct(\Closure $handle)
    {
        $this->subProcessHandle = $handle;

        $this->init();
    }

    /**
     * 进程的初始化
     *
     * @return void
     */
    private function init()
    {
        declare(ticks = 1);

        //注册信号监听
        $this->registerSignalHandler();
        //设置守护进程
        $this->daemonize();
        //启动子进程
        $this->startSubProcess();
        //等待子进程
        $this->wait();
    }

    /**
     * 使进程变为守护进程
     *
     * @return void
     *
     * @throws Exception
     */
    private function daemonize()
    {
        //只有在cli模式下可以变为守护进程
        if (php_sapi_name() != 'cli') {
            return;
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

    /**
     * 启动子进程
     *
     * @return void
     */
    private function startSubProcess()
    {
        for ($i=0; $i<$this->subProcessMaxCount; $i++) {
            $this->makeSubProcess();
        }
    }

    /**
     * 生成子进程
     *
     * @return void
     *
     * @throws Exception
     */
    private function makeSubProcess()
    {
        $pid = pcntl_fork();

        if ($pid == 0) {
            //子进程
            $subProcess = new SubProcess();
            $subProcess->handler($this->subProcessHandle);
        } elseif ($pid > 0) {
            //父进程
            $this->subProcessPidMap[$pid] = $pid;
        } else {
            //错误
            throw new Exception('fork fail in make sub process');
        }
    }

    /**
     * 父进程监听子进程
     * 等待子进程退出
     *
     * @return void
     */
    private function wait()
    {
        while (true) {
            $status = 0;

            //等待子进程退出
            $pid = pcntl_wait($status, WUNTRACED);

            if ($pid > 0) {
                unset($this->subProcessPidMap[$pid]);
            }

            sleep($this->sleepTime);
        }
    }

    /**
     * 信号处理
     *
     * @param  int $signal
     * @return void
     */
    protected function signalHandler($signal)
    {

    }
}