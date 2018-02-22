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
	 * 进程名称
	 * 
	 * @var string
	 */
	protected $processName = 'crawler worker';

    public function __construct()
    {
		parent::__construct();
        $this->registerSignalHandler();
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
                exit(0);
                break;
        }
    }
}
