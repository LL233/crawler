<?php

namespace Crawler;

use Crawler\Container\Container;
use Crawler\Container\RegisterComponents;

/**
 * 爬虫的入口函数
 * 负责爬虫的基本配置和组件注入
 * 负责启动和停止爬虫
 *
 * @author LL
 */
class Crawler
{
    /**
     * 容器实例
     *
     * @var \Crawler\Container\Container
     */
    private $container;

    /**
     * 构造函数
     * 对爬虫进行基本的配置
     *
     * @param array $config 配置数组
     */
    public function __construct($config)
    {
    }

    /**
     * 初始化容器
     * 
     * @return void
     */
    private function initContainer()
    {
        $this->container = Container::getInstance();
    }

    /**
     * 注册基础组件
     *
     * @return void
     */
    private function registerComponents()
    {
        $registerComponents = new RegisterComponents();
        $registerComponents->register();
    }
}