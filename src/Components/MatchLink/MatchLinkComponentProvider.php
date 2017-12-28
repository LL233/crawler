<?php

namespace Crawler\Components\MatchLink;

use Crawler\ComponentProvider;

/**
 * MatchLink组件提供者
 *
 * @author LL
 */
class MatchLinkComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('MatchLink', function(){
            return new \Crawler\Components\MatchLink\MatchLinkTag();
        });
    }
}