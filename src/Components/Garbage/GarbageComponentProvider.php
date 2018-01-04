<?php

namespace Crawler\Components\Garbage;

use Crawler\ComponentProvider;

/**
 * Garbage的组件提供者
 *
 * @author LL
 */
class GarbageComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Garbage', function(){
            return new MemoryGarbage();
        });
    }
}