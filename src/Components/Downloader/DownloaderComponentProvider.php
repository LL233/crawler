<?php

namespace Crawler\Components\Downloader;

use Crawler\ComponentProvider;
use Crawler\EventTag;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Downloader组件提供者
 *
 * @author LL
 */
class DownloaderComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->container->bind('Downloader', function($container){
            return new HttpClient(
                $container->make('Client'),
                $container->make('EventDispatcher'),
                $container->make('HttpClientParser')
            );
        });

        $this->container->bind('Client', function(){
            return new \GuzzleHttp\Client([
                'timeout' => 2.0
            ]);
        });

        $this->container->bind('HttpClientBaseEvent', function($container){
            return new HttpClientBaseEvent($container->make('LinkTag'), $container->make('Config'));
        });

        $this->container->bind('HttpClientParser', function(){
            return new HttpClientParser();
        });

        $this->container->bind('RequestEvent', function($container, $params){
            return new RequestEvent($params['downloader']);
        }, true);
    }

    public function listen(EventDispatcher $dispatcher): void
    {
        $dispatcher->addListener(EventTag::REQUEST_BEFORE, function(RequestEvent $requestEvent){
            $httpClientBaseEvent = $this->container->make('HttpClientBaseEvent');
            $httpClientBaseEvent->baseEvent($requestEvent->downloader);
        });
    }
}