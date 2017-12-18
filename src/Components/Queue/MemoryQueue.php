<?php

namespace Crawler\Components\Queue;

/**
 * 基于php数组实现的队列
 *
 * @author LL
 */
class MemoryQueue implements QueueInterface
{
    private $queue = [];

    /**
     * 入队
     *
     * @param mixed $value
     * @return void
     */
    public function push($value)
    {
        array_push($this->queue, $value);
    }

    /**
     * 出队
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->queue);
    }

    /**
     * 判断是否在队列中
     *
     * @param  mixed $value
     * @return bool
     */
    public function has($value)
    {
        return in_array($this->queue, $value);
    }

    /**
     * 判断队列是否为空
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->queue);
    }
}