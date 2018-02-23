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
            $config = new \Crawler\Components\ConfigSetting\ConfigSetting($params['config']);

            //默认的爬虫配置
            //即使用户设置了一些配置，在这里也将覆盖，保证关键配置的正确性
            $config['taskConfig'] = [
                //爬虫任务
                'spider' => [
                    'count' => 4,
                    'handle' => function() use ($container){
                        $spiderController = $container->make('SpiderController');

                        $spiderController->start();
                    }
                ]
            ];
            //爬虫的休眠时间
            if (!isset($config['sleepTime'])) {
                $config['sleepTime'] = 1;
            }

            return $config;
        });
    }
}