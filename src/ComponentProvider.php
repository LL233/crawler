<?php

namespace Crawler;

use Crawler\Container\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * 组件提供者接口
 * 构造方法中不允许传入参数
 * register方法中完成组件的注册
 *
 * @author LL
 */
abstract class ComponentProvider
{
    /**
     * 容器实例
     *
     * @var \Crawler\Container\Container
     */
    protected $container;

    /**
     * 构造函数中获取容器实例
     * 提供给register方法中使用
     */
    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * 在这个方法中，通过调用Container对象中的bind方法来注册好组件
     */
    abstract public function register(): void;

    /**
     * 在这个方法中，组件可以注册事件监听
     *
     * @param EventDispatcher $dispatcher
     */
    public function listen(EventDispatcher $dispatcher): void {}
}