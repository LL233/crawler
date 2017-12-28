<?php

namespace Crawler\Container;

use Crawler\ComponentProvider;

/**
 * 将基本的组件在这里注册好
 *
 * @author LL
 */
class RegisterComponents
{
    /**
     * 容器实例
     *
     * @var Container
     */
    private $container;

    /**
     * 组件提供者
     *
     * @var array
     */
    private $componentProviders = [
        \Crawler\Components\ConfigSetting\ConfigComponentProvider::class,
        \Crawler\Components\Downloader\DownloaderComponentProvider::class,
        \Crawler\Components\Filter\FilterComponentProvider::class,
        \Crawler\Components\MatchLink\MatchLinkComponentProvider::class,
        \Crawler\Components\Parser\ParserComponentProvider::class,
        \Crawler\Components\Queue\QueueComponentProvider::class,
        \Crawler\Components\Spider\SpiderComponentProvider::class,
        \Crawler\Components\SpiderController\SpiderControllerComponentProvider::class,
        \Crawler\Components\MultiProcess\MultiProcessComponentProvider::class
    ];

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * 注册组件
     *
     * @throws \Exception
     */
    public function registerComponent()
    {
        //先注册基础组件
        $this->registerBaseComponent();

        //循环所有组件提供者
        foreach ($this->componentProviders as $componentProvider) {
            $instance = new $componentProvider();

            if (!$instance instanceof ComponentProvider) {
                throw new \Exception("{$componentProvider} not implement \\Crawler\\ComponentProvider");
            }

            call_user_func([$instance, 'register']);
        }
    }

    /**
     * 注册基本组件
     *
     * @return void
     */
    private function registerBaseComponent()
    {
        $this->container->bind('EventDispatcher', function(){
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });

        $this->container->bind('SpiderEvent', function($container, $params){
            return new \Crawler\Events\SpiderEvent($params['spider'], $params['params']);
        }, true);

        $this->container->bind('Cookie', function(){
            return new \GuzzleHttp\Cookie\FileCookieJar(__DIR__.'/cookie');
        });

        $this->container->bind('RequestEvent', function($container, $params){
            return new \Crawler\Events\RequestEvent($params['downloader']);
        }, true);
    }
}