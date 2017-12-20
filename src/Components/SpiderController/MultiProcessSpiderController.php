<?php

namespace Crawler\Components\SpiderController;

use Crawler\Components\Spider\MultiSpider;

/**
 * 基于多进程实现的爬虫控制器
 *
 * @author LL
 */
class MultiProcessSpiderController implements SpiderControllerInterface
{
    private $spider;

    public function __construct(MultiSpider $spider)
    {
        $this->spider = $spider;
    }

    /**
     * 启动爬虫
     *
     * @return void
     */
    public function start(): void
    {
        while (($link = $this->spider->next())) {
            $response = $this->spider->getContent($link);
            $this->spider->filterData($response);
        }

        $this->stop();
    }

    /**
     * 停止爬虫
     *
     * @return void
     */
    public function stop(): void
    {
        $this->spider->end();
    }
}