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
            return new \Guzzle\Http\Client();
        });
    }
}