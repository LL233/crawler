<?php

namespace Crawler\Components\SpiderController;

use Crawler\Components\Spider\MultiSpider;
use Crawler\Container\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Crawler\EventTag;

/**
 * 基于多进程实现的爬虫控制器
 *
 * @author LL
 */
class MultiProcessSpiderController implements SpiderControllerInterface
{
    /**
     * 爬虫引擎实例
     *
     * @var MultiSpider
     */
    private $spider;

    /**
     * 容器实例
     *
     * @var Container
     */
    private $container;

    /**
     * 事件派发
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(MultiSpider $spider, Container $container, EventDispatcher $event)
    {
        $this->spider = $spider;
        $this->container = $container;
        $this->eventDispatcher = $event;
    }

    /**
     * 启动爬虫
     *
     * @return void
     */
    public function start(): void
    {
        //爬虫启动事件派发
        $this->dispatch(EventTag::SPIDER_START);

        while (true) {
            $link = $this->spider->next();

            try {
                $parser = $this->spider->getContent($link);
                $this->spider->filterData($parser);
            } catch (\Exception $e) {
                //TODO:跳过本次循环，并触发一个事件
                continue;
            } finally {
                //TODO:触发一个本次抓取结束的事件
            }
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

    /**
     * 事件派发
     *
     * @param string $eventTag 事件名称
     */
    private function dispatch(string $eventTag): void
    {
        $this->eventDispatcher->dispatch($eventTag, $this->container->make('SpiderEvent', [
            'spider' => $this->spider
        ]));
    }
}