<?php

namespace Crawler\Components\Garbage;

/**
 * 链接垃圾堆
 * 存储已经抓取过的链接
 * 防止重复抓取
 * 应采取一定的策略，防止垃圾堆过大
 *
 * @author LL
 */
interface GarbageInterface
{
    /**
     * 将数据放入垃圾堆
     *
     * @param array $data
     */
    public function put(array $data): void;

    /**
     * 统计垃圾堆中数据的数量
     *
     * @return int
     */
    public function count(): int;

    /**
     * 判断这个数据是否存在于垃圾堆中
     *
     * @param  mixed $data
     * @return bool
     */
    public function has($data): bool;

    /**
     * 清空垃圾堆
     */
    public function clear(): void;

    /**
     * 将数组中与垃圾堆中重复的数据删除
     * 并将删除后的数组返回
     *
     * @param  array $data
     * @return array
     */
    public function removeRepeat(array $data): array;
}