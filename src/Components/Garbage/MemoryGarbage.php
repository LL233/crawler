<?php

namespace Crawler\Components\Garbage;

/**
 * 基于内存的垃圾存储
 *
 * @author LL
 */
class MemoryGarbage implements GarbageInterface
{
    /**
     * 存储数据的数组
     *
     * @var array
     */
    private $garbageData = [];

    /**
     * 将数据放入垃圾堆中
     *
     * @param mixed $data
     */
    public function put($data): void
    {
        if (is_array($data)) {
            array_merge($this->garbageData, $data);
        } else {
            array_push($this->garbageData, $data);
        }
    }

    /**
     * 统计垃圾堆中数据的数量
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->garbageData);
    }

    /**
     * 判断这个数据是否存在于垃圾堆中
     *
     * @param  mixed $data
     * @return bool
     */
    public function isIn($data): bool
    {
        return in_array($data, $this->garbageData);
    }

    /**
     * 清空垃圾堆
     */
    public function clean(): void
    {
        unset($this->garbageData);
    }

}