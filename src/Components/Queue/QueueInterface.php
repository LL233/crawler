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
     * @param mixed $value
     * @return void
     */
    public function push($value);

    /**
     * 出队
     *
     * @return mixed
     */
    public function pop();

    /**
     * 判断是否在队列中
     *
     * @param  mixed $value
     * @return bool
     */
    public function has($value);

    /**
     * 判断队列是否为空
     *
     * @return bool
     */
    public function isEmpty();
}