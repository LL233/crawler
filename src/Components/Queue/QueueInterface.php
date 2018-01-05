<?php

namespace Crawler\Components\Queue;

/**
 * 抓取队列接口
 *
 * @author LL
 */
interface QueueInterface
{
    /**
     * 入队
     *
     * @param  array $value
     * @return void
     */
    public function push(array $value): void;

    /**
     * 出队
     *
     * @return string
     */
    public function pop(): string;

    /**
     * 判断是否在队列中
     *
     * @param  string $value
     * @return bool
     */
    public function has(string $value): bool;

    /**
     * 判断队列是否为空
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * 将数组中与队列中重复的数据删除
     * 并将删除后的数组返回
     *
     * @param  array $data
     * @return array
     */
    public function removeRepeat(array $data): array;
}