<?php

namespace Crawler\Components\Queue;

/**
 * 基于php数组实现的队列
 *
 * @author LL
 */
class MemoryQueue implements QueueInterface
{
    /**
     * 以数组形式保存队列
     *
     * @var array
     */
    private $queue = [];

    /**
     * 入队
     *
     * @param  array $value
     * @return void
     */
    public function push(array $value): void
    {
        array_push($this->queue, $value);
    }

    /**
     * 出队
     *
     * @return string
     */
    public function pop(): string
    {
        return ($popData = array_pop($this->queue)) != null ? $popData : '';
    }

    /**
     * 判断是否在队列中
     *
     * @param  string $value
     * @return bool
     */
    public function has(string $value): bool
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