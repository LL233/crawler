<?php

namespace Crawler\Components\SpiderController;

use Crawler\ComponentProvider;

/**
 * SpiderController组件提供者
 *
 * @author LL
 */
class SpiderControllerComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('SpiderController', function($container){
            $config = $container->make("Config");

            return new MultiProcessSpiderController($container->make('Spider'), $container->make('EventDispatcher'), $config['sleepTime']);
        });
    }
}