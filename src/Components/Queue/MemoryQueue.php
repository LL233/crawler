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
    public function isEmpty(): bool
    {
        return empty($this->queue);
    }

    /**
     * 将数组中与队列中重复的数据删除
     * 并将删除后的数组返回
     *
     * @param  array $data
     * @return array
     */
    public function removeRepeat(array $data): array
    {
        foreach ($data as $k=>$v) {
            if ($this->has($v)) {
                unset($data[$k]);
            }
        }

        return $data;
    }
}