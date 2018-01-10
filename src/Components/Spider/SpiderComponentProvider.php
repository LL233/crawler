<?php

namespace Crawler\Components\Spider;

use Crawler\ComponentProvider;

/**
 * Spider组件提供者
 *
 * @author LL
 */
class SpiderComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Spider', function($container){
            return new MultiSpider(
                $container->make('Downloader'),
                $container->make('LinkManager'),
                $container->make('Filter'),
                $container->make('LinkTag'),
                $container->make('EventDispatcher')
            );
        });

        $this->container->bind('SpiderEvent', function($container, $params){
            return new SpiderEvent($params['spider'], $params['params']);
        }, true);
    }
}