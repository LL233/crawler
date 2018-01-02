<?php

namespace Crawler\Components\Downloader;

use Crawler\ComponentProvider;

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
                $container->make('Event'),
                $container->make('HttpClientParser')
            );
        });

        $this->container->bind('Client', function(){
            return new \GuzzleHttp\Client([
                'timeout' => 2.0
            ]);
        });

        $this->container->bind('HttpClientBaseEvent', function($container){
            return new HttpClientBaseEvent($container->make('Spider'), $container->make('Config'));
        });

        $this->container->bind('HttpClientParser', function(){
            return new HttpClientParser();
        });

        $this->container->bind('RequestEvent', function($container, $params){
            return new RequestEvent($params['downloader']);
        }, true);
    }
}