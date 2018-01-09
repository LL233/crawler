<?php

namespace Crawler\Components\LinkManager;

use Crawler\Components\Queue\QueueInterface;
use Crawler\Components\Garbage\GarbageInterface;

/**
 * 链接管理
 * 负责获取和保存链接
 * 依赖于队列和回收堆实现
 *
 * @author LL
 */
class LinkManager implements LinkManagerInterface
{
    /**
     * 保存链接的队列
     *
     * @var QueueInterface
     */
    private $queue;

    /**
     * 保存已使用过的链接的回收堆
     *
     * @var GarbageInterface
     */
    private $garbage;

    /**
     * 回收堆的清除阈值
     *
     * @var int
     */
    private $garbageClearMax;

    /**
     * @param QueueInterface   $queue
     * @param GarbageInterface $garbage
     * @param int              $garbageClearMax
     */
    public function __construct(QueueInterface $queue, GarbageInterface $garbage, int $garbageClearMax)
    {
        $this->queue = $queue;
        $this->garbage = $garbage;
        $this->garbageClearMax = $garbageClearMax;
    }

    /**
     * 获取一个链接
     *
     * @return string
     */
    public function getLink(): string
    {
        if (!$this->queue->isEmpty()) {
            $link = $this->queue->pop();

            //在将链接加入回收堆之前先清理回收堆
            $this->cleanGarbage();
            //将取出的链接保存到回收堆
            $this->garbage->put($link);

            return $link;
        } else {
            return '';
        }
    }

    /**
     * 保存链接
     *
     * @param  array $link
     */
    public function saveLink(array $links): void
    {
        if (!empty($link)) {
            //将已保存在队列中的链接删除
            $links = $this->queue->removeRepeat($links);
            //将已保存在回收堆中的链接删除
            $links = $this->garbage->removeRepeat($links);

            $this->queue->push($links);
        }
    }

    /**
     * 清除回收堆
     */
    private function cleanGarbage(): void
    {
        //如果回收堆中数据的数量已经达到最大值，则执行清除
        //如果最大值为0，则永远不执行清除
        if ($this->garbageClearMax != 0 && $this->garbage->count() >= $this->garbageClearMax) {
            $this->garbage->clean();
        }
    }
}