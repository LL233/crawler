<?php

namespace Crawler\Components\Queue;

use Crawler\ComponentProvider;
use Crawler\Components\Spider\SpiderEvent;
use Crawler\EventTag;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Queue组件提供者
 *
 * @author LL
 */
class QueueComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Queue', function(){
            return new \Crawler\Components\Queue\MemoryQueue();
        });
    }

    public function listen(EventDispatcher $dispatcher): void
    {
        //爬虫启动时，如果队列为空，则获取配置中的startLink
        $dispatcher->addListener(EventTag::SPIDER_START, function(SpiderEvent $event){
            $queue = $this->container->make('Queue');

            if ($queue->isEmpty()) {
                $config = $this->container->make('Config');
                $queue->push($config['startLink']);
            }
        });

        //将过滤出的链接保存进队列
        $dispatcher->addListener(EventTag::SPIDER_FILTER_CONTENT_AFTER, function(SpiderEvent $event){
            $this->container->make('Queue')->push($event['linkRes']);
        }, 100);
    }
}