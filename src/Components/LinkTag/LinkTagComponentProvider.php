<?php

namespace Crawler\Components\LinkTag;

use Crawler\ComponentProvider;

/**
 * LinkTag组件提供者
 *
 * @author LL
 */
class LinkTagComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('LinkTag', function(){
            return new LinkTag();
        });
    }
}