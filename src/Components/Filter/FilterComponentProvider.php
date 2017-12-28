<?php

namespace Crawler\Components\Filter;

use Crawler\ComponentProvider;

/**
 * Filter组件提供者
 *
 * @author LL
 */
class FilterComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Filter', function(){
            return new \Crawler\Components\Filter\SimpleFilter('default');
        });
    }
}