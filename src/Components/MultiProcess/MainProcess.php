<?php

namespace Crawler\Components\MultiProcess;

use Crawler\Container\Container;
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
     * 是否守护进程化
     *
     * @var bool
     */
    private $isDaemonize;

    /**
     * 保存子进程的进程id
     *
     * @var array
     */
    private $subProcessPidMap = [];

    /**
     * task任务的配置
     *
     * @var array
     */
    private $taskConfig = [];

    /**
     * 进程休眠时间
     *
     * @var float
     */
    private $sleepTime = 0.1;

    /**
     * 进程是否处于停止状态
     *
     * @var int
     */
    private $stopStatus = 0;

    /**
     * 进程是否处于重启状态
     *
     * @var int
     */
    private $restartStatus = 0;

    /**
     * 已经完成重启的子进程的数量
     *
     * @var int
     */
    private $restartSubProcessCount = 0;

    /**
     * 标准输出重定向位置
     *
     * @var string
     */
    private $stdoutFilePath = '/dev/null';
	
	/**
	 * 进程名称
	 *
	 * @var string
	 */
	protected $processName = 'crawler main';

    /**
     * 主进程构造函数
     * 设置信号监听
     *
     * $taskConfig = [
     *      "taskA" => [
     *          "count" => 4,
     *          "handle" => function(){}
     *      ],
     *      "taskB" => [
     *          "count" => 1,
     *          "handle" => function(){}
     *      ]
     * ]
     *
     * 子进程的数量取决于所有task的分配子进程的数量
     *
     * @param array $taskConfig  子进程的任务配置
     * @param bool  $isDaemonize 是否以守护进程启动
     */
    public function __construct(array $taskConfig, $isDaemonize = false)
    {
		parent::__construct();
		
        $this->taskConfig = $taskConfig;
        $this->isDaemonize = $isDaemonize;

        $this->init();
    }

    /**
     * 进程的初始化
     *
     * @return void
     */
    private function init(): void
    {
        //检车taskConfig配置是否正确
        $this->checkTaskConfig();
        //注册信号监听
        $this->registerSignalHandler();
        //设置守护进程
        $this->daemonize();
        //重定向标准输出
        $this->resetStdout();
        //保存pid
        $this->savePid();
        //启动子进程
        $this->startSubProcess();
        //等待子进程
        $this->wait();
    }

    /**
     * 检查taskConfig的配置是否正确
     *
     * @throws Exception
     */
    private function checkTaskConfig(): void
    {
        foreach ($this->taskConfig as $task) {
            if (!is_array($task) || !isset($task['count']) || !isset($task['handle']) || !$task['handle'] instanceof \Closure || $task['count'] <= 0) {
                throw new \Exception('task config error');
            }
        }
    }

    /**
     * 使进程变为守护进程
     *
     * @return void
     *
     * @throws Exception
     */
    private function daemonize(): void
    {
        //只有在cli模式下可以变为守护进程
        if (php_sapi_name() != 'cli' || !$this->isDaemonize) {
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
    private function startSubProcess(): void
    {
        foreach ($this->taskConfig as $task) {
            for ($i = 0; $i < $task['count']; $i++) {
                $this->makeSubProcess($task['handle']);
            }
        }
    }

    /**
     * 生成子进程
     *
     * @param  \Closure $handle
     * @return void
     *
     * @throws Exception
     */
    private function makeSubProcess(\Closure $handle): void
    {
        $pid = pcntl_fork();

        if ($pid == 0) {
            //生成子进程
            $subProcess = Container::getInstance()->make('SubProcess');
            $subProcess->handler($handle);
        } elseif ($pid > 0) {
            //父进程
            $this->subProcessPidMap[$pid] = $handle;
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
    private function wait(): void
    {
        while (true) {
            $status = 0;

            //等待子进程退出
            $pid = pcntl_wait($status, WNOHANG);

            if ($pid > 0) {
                //如果进程处于停止状态
                if ($this->stopStatus != 0) {
                    $this->stopHandle($pid);
                }
                //如果进程处于重启状态
                if ($this->restartStatus != 0) {
                    $this->restartHandler($pid);
                }
                //如果进程是异常退出
                if ($this->stopStatus == 0 && $this->restartStatus == 0) {
                    $this->exceptionProcessHandler($pid, $status);
                }
            }

            if (count($this->subProcessPidMap) == 0) {
                exit(parent::STOP_EXIT);
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
    public function signalHandler(int $signal): void
    {
        switch ($signal) {
            //退出
            case SIGINT :
            case SIGTERM :
                echo "is stop\n";
                $this->stop();
                break;
            //重启
            case SIGUSR1:
                $this->restart();
                break;
        }
    }

    /**
     * 进程退出
     * 等待并回收全部子进程后退出
     *
     * @return void
     */
    private function stop(): void
    {
        $this->stopStatus = 1;

        $this->killSubProcess();
    }

    /**
     * 重启子进程
     *
     * @return void
     */
    private function restart(): void
    {
        $this->restartStatus = 1;

        $this->killSubProcess();
    }

    /**
     * 结束子进程
     *
     * @return void
     */
    private function killSubProcess(): void
    {
        $pidMap = array_keys($this->subProcessPidMap);

        foreach ($pidMap as $pid) {
            //向所有子进程发送退出信号
            posix_kill($pid, SIGTERM);
        }
    }

    /**
     * 进程停止的操作
     *
     * @param  int $pid
     * @return void
     */
    private function stopHandle(int $pid): void
    {
        if (isset($this->subProcessPidMap[$pid])) {
            unset($this->subProcessPidMap[$pid]);
        }
    }

    /**
     * 重启进程的操作
     *
     * @param  int $pid
     * @return void
     */
    private function restartHandler(int $pid): void
    {
        if (isset($this->subProcessPidMap[$pid])) {
            try {
                $this->makeSubProcess($this->subProcessPidMap[$pid]);
                unset($this->subProcessPidMap[$pid]);
                $this->restartSubProcessCount++;

            } catch (Exception $e) {
                //TODO:记录日志
            }
        }

        //如果重启子进程的数量已经达到子进程最大的数量，则停止重启状态
        if ($this->restartSubProcessCount == count($this->subProcessPidMap)) {
            $this->restartStatus = 0;
            $this->restartSubProcessCount = 0;
        }
    }

    /**
     * 异常退出的子进程
     *
     * @param  int $pid    子进程的id
     * @param  int $status 子进程的退出状态
     * @return void
     */
    private function exceptionProcessHandler(int $pid, int $status): void
    {
        if (isset($this->subProcessPidMap[$pid])) {
            //如果子进程是完成任务退出，则不重启
            if (pcntl_wifexited($status)) {
                if (pcntl_wexitstatus($status) == parent::STOP_EXIT) {
                    unset($this->subProcessPidMap[$pid]);

                    return;
                }
            }

            //重启一个子进程
            $this->makeSubProcess($this->subProcessPidMap[$pid]);
            unset($this->subProcessPidMap[$pid]);


            //TODO:记录子进程的退出状态
        }
    }

    /**
     * 重定向标准输出和标准错误输出
     *
     * @return void
     */
    private function resetStdout(): void
    {
        if (!$this->isDaemonize) {
            return;
        }

        global $STDOUT, $STDERR;

        //关闭标准输出和标准错误输出
        @fclose(STDOUT);
        @fclose(STDERR);

        $STDOUT = fopen($this->stdoutFilePath, 'a');
        $STDERR = fopen($this->stdoutFilePath, 'a');
    }
}
