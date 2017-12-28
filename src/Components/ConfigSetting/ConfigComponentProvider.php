<?php

namespace Crawler\Components\ConfigSetting;

use Crawler\ComponentProvider;

/**
 * Config组件提供者
 *
 * @author LL
 */
class ConfigComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Config', function($container, $params){
            return new \Crawler\Components\ConfigSetting\ConfigSetting($params['config']);
        });
    }
}