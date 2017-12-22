<?php

namespace Crawler\Components\Queue;

/**
 * 队列管理器
 *
 * @author LL
 */
class QueueManage
{
    /**
     * 队列
     *
     * @var QueueInterface
     */
    protected $queue;

    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * 队列中增加数据
     *
     * @param  array $data
     * @return void
     */
    public function add(array $data): void
    {
        foreach ($data as $v) {
            if (!$this->queue->has($v)) {
                $this->queue->push($v);
            }
        }
    }

    /**
     * 从队列中获取数据
     *
     * @return mixed
     */
    public function get()
    {
        return $this->queue->isEmpty() == true ? false : $this->queue->pop();
    }
}