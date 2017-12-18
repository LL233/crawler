<?php

namespace Crawler\Components\Spider;

use Crawler\Components\Parser\ParserInterface;
use Crawler\Components\Downloader\DownloaderInterface;
use Crawler\Components\Queue\QueueInterface;

/**
 * MultiSpider抓取引擎
 *
 * @author LL
 */
class MultiSpider implements SpiderInterface
{
    /**
     * 下载器
     *
     * @var DownloaderInterface
     */
    private $downloader;

    /**
     * 队列
     *
     * @var QueueInterface
     */
    private $queue;

    public function __construct(DownloaderInterface $downloader, QueueInterface $queue)
    {
        $this->downloader = $downloader;
        $this->queue = $queue;
    }

    /**
     * 获取抓取内容
     *
     * @return mixed
     */
    public function getContent()
    {

    }

    /**
     * 清洗数据
     *
     * @return mixed
     */
    public function filterData()
    {

    }

    /**
     * 准备下一次的抓取
     *
     * @return mixed
     */
    public function next()
    {

    }

    /**
     * 抓取结束
     *
     * @return mixed
     */
    public function end()
    {

    }
}