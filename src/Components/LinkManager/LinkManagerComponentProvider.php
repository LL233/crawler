<?php

namespace Crawler\Components\LinkManager;

use Crawler\ComponentProvider;
use Crawler\Components\Spider\SpiderEvent;
use Crawler\EventTag;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * LinkDispatch组件提供者
 *
 * @author LL
 */
class LinkManagerComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('LinkManager', function($container){
            $config = $container->make('Config');
            //清除回收堆的最大值，默认为10000
            $garbageClearMax = isset($config['garbageClearMax']) ? $config['garbageClearMax'] : 10000;

            return new LinkManager($container->make('Queue'), $container->make('Garbage'), $garbageClearMax);
        });
    }

    public function listen(EventDispatcher $dispatcher): void
    {
        //爬虫启动时，如果队列为空，则将入口链接保存
        //否则认为程序已经启动过，则不将入口链接保存
        $dispatcher->addListener(EventTag::SPIDER_START, function(){
            $linkManager = $this->container->make('LinkManager');
            $queue = $this->container->make('Queue');

            if ($queue->isEmpty()) {
                $config = $this->container->make('Config');

                $linkManager->saveLink([$config['startLink']]);
            }
        }, 100);

        //爬虫过滤完内容后，将链接保存
        $dispatcher->addListener(EventTag::SPIDER_FILTER_CONTENT_AFTER, function(SpiderEvent $event){
            $this->container->make('LinkManager')->saveLink($event['linkRes']);
        }, 100);
    }
}