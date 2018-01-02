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
                $container->make('Queue'),
                $container->make('Filter'),
                $container->make('MatchLink'),
                $container->make('Event'),
                $container
            );
        });

        $this->container->bind('SpiderEvent', function($container, $params){
            return new SpiderEvent($params['spider'], $params['params']);
        }, true);
    }
}