<?php

namespace Crawler\Components\Queue;

use Crawler\ComponentProvider;

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
}