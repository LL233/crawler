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
     * 进程退出时的状态码
     *
     * @var int
     */
    const STOP_EXIT = 233;
	
	/**
	 * 进程名字
	 *
	 * @var string
	 */
	protected $processName = 'none';

	public function __construct()
	{
		$this->setProcessName();
	}
	
	/**
	 * 设置进程名称
	 *
	 * @param void
	 */
	private function setProcessName(): void
	{
		if (function_exists('cli_set_process_title')) {
			cli_set_process_title($this->processName);
		}
	}

    /**
     * 信号处理
     *
     * @param  int $signal
     * @return void
     */
    abstract function signalHandler(int $signal): void;

    /**
     * 注册信号监听
     *
     * @return void
     */
    protected function registerSignalHandler(): void
    {
		//基于php7.2版本提供的异步信号监听
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, [$this, "signalHandler"]);
        pcntl_signal(SIGINT, [$this, "signalHandler"]);
        pcntl_signal(SIGUSR1, [$this, "signalHandler"]);
    }

    /**
     * 获取保存pid文件的路径
     *
     * @return string
     */
    protected function getSavePidPath(): string
    {
        return '/var/run/crawler.pid';
    }

    /**
     * 保存pid
     *
     * @return void
     */
    protected function savePid(): void
    {
        $pid = posix_getpid();

        file_put_contents($this->getSavePidPath(), $pid);
    }
}
