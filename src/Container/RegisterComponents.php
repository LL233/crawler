<?php

namespace Crawler\Container;

use Crawler\Container\Container;

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

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * 注册基本组件
     *
     * @return void
     */
    public function register()
    {
        //配置组件
        $this->container->bind('Config', function($app, $params){
            return new \Crawler\Components\ConfigSetting\ConfigSetting($params['config']);
        });

        //\DiDom\Document
        $this->container->bind('Document', function($app){
            return new \DiDom\Document();
        });

        //\Guzzle\Http\Client
        $this->container->bind('Client', function($app){
            return new \GuzzleHttp\Client([
                'timeout' => 2.0
            ]);
        });

        $this->container->bind('Downloader', function($app){
            return new \Crawler\Components\Downloader\HttpClient($app->make('Client'), $app->make('Event'));
        });

        $this->container->bind('HtmlParser', function($app){
            return new \Crawler\Components\Parser\HtmlParser($app->make('Document'));
        });

        $this->container->bind('JsonParser', function($app){
            return new \Crawler\Components\Parser\JsonParser();
        });

        $this->container->bind('RegexParser', function($app){
            return new \Crawler\Components\Parser\RegexParser();
        });

        $this->container->bind('Queue', function($app){
            return new \Crawler\Components\Queue\MemoryQueue();
        });

        $this->container->bind('MultiProcess', function($app, $params){
            return new \Crawler\Components\MultiProcess\MainProcess($params['handler'], $params['subProcessCount']);
        });

        $this->container->bind('Spider', function($app){
            return new \Crawler\Components\Spider\MultiSpider(
                $app->make('Downloader'),
                $app->make('Queue'),
                $app->make('Filter'),
                $app->make('MatchLink'),
                $app->make('Event'),
                $app
            );
        });

        $this->container->bind('Event', function($app){
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });

        $this->container->bind('SpiderEvent', function($app, $params){
            return new \Crawler\EventListener\Events\SpiderEvent($params['spider'], $params['params']);
        }, true);

        $this->container->bind('FileCookie', function($app){
            return new \GuzzleHttp\Cookie\FileCookieJar(__DIR__.'/cookie');
        });

        $this->container->bind('RequestEvent', function($app, $params){
            return new \Crawler\EventListener\Events\RequestEvent($params['downloader']);
        }, true);

        $this->container->bind('HttpClientBaseEvent', function($app){
            return new \Crawler\Components\Downloader\HttpClientBaseEvent($app->make('Spider'), $app->make('Config'));
        });

        $this->container->bind('MatchLink', function($app){
            return new \Crawler\Components\MatchLink\MatchLinkTag();
        });

        $this->container->bind('Filter', function($app){
            return new \Crawler\Components\Filter\SimpleFilter('default');
        });
    }
}