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
            return new \Crawler\Components\Spider\MultiSpider(
                $container->make('Downloader'),
                $container->make('Queue'),
                $container->make('Filter'),
                $container->make('MatchLink'),
                $container->make('Event'),
                $container
            );
        });
    }
}