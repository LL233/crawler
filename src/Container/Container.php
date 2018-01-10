<?php

namespace Crawler\Container;

use Closure;
use Exception;
use Crawler\ComponentProvider;

/**
 * 组件容器
 * 负责加载所有用到的组件
 * 负责提供所有用到的组件
 * 组件以按需加载的方式提供
 *
 * @author LL
 */
class Container
{
    /**
     * 全局可用容器实例
     *
     * @var static
     */ 
    private static $instance;

    /**
     * 注册到容器的别名
     *
     * @var array
     */
    private $aliases = [];

    /**
     * 容器中可以共享使用的实例
     *
     * @var array
     */
    private $instances = [];

    /**
     * 组件提供者
     *
     * @var array
     */
    private $providerMap = [
        \Crawler\Components\ConfigSetting\ConfigComponentProvider::class,
        \Crawler\Components\Downloader\DownloaderComponentProvider::class,
        \Crawler\Components\Filter\FilterComponentProvider::class,
        \Crawler\Components\LinkTag\LinkTagComponentProvider::class,
        \Crawler\Components\Parser\ParserComponentProvider::class,
        \Crawler\Components\Queue\QueueComponentProvider::class,
        \Crawler\Components\Spider\SpiderComponentProvider::class,
        \Crawler\Components\SpiderController\SpiderControllerComponentProvider::class,
        \Crawler\Components\MultiProcess\MultiProcessComponentProvider::class,
        \Crawler\Components\LinkManager\LinkManagerComponentProvider::class,
        \Crawler\Components\Garbage\GarbageComponentProvider::class
    ];

    /**
     * 设置并返回全局可用容器实例
     * 
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 注册绑定
     *
     * @param  string  $abstract 绑定的名字
     * @param  Closure $concrete 实例的制造方法
     * @param  bool    $isOnly   是否为单例
     * @return void
     *
     * @throws Exception
     */
    public function bind($abstract, Closure $concrete, $isOnly = false)
    {   
        //如果已经绑定，则抛出异常
        if (isset($this->instances[$abstract])) {
            throw new Exception("this {$abstract} already registered");
        }

        $this->aliases[$abstract] = [
            "concrete" => $concrete,
            "isOnly" => $isOnly
        ];
    }

    /**
     * 解析并返回指定的实例
     *
     * @param  string $abstract 实例的别名
     * @param  array  $params   执行实例的制造方法时自定义参数
     * @return mixed 
     *
     * @throws Exception
     */
    public function make($abstract, array $params = [])
    {
        //如果没有注册别名，则抛出异常
        if (!isset($this->aliases[$abstract])) {
            throw new Exception("this {$abstract} is not registered");
        }

        //如果这个别名绑定的实例已经存在，则返回这个实例
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $instance = $this->build($abstract, $params);

        //如果这不是一个单例模式的实例，就把它加入实例组中
        if (!$this->aliases[$abstract]['isOnly']) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * 编译出一个实例
     *
     * @param  string $abstract 实例的别名
     * @param  array  $params   执行实例的制造方法时的自定义参数
     * @return mixed
     */
    private function build($abstract, array $params = [])
    {
        $concrete = $this->aliases[$abstract]['concrete'];

        return call_user_func_array($concrete, [$this, $params]);
    }

    /**
     * 注册组件
     *
     * @throws Exception
     */
    public function register(): void
    {
        $this->registerBaseComponent();

        //循环所有组件提供者
        foreach ($this->providerMap as $provider) {
            $instance = new $provider();

            if (!$instance instanceof ComponentProvider) {
                throw new \Exception("{$provider} not implement \\Crawler\\ComponentProvider");
            }

            //执行register方法，绑定容器
            call_user_func([$instance, 'register']);
            //执行listen方法，绑定事件
            call_user_func([$instance, 'listen'], $this->make('EventDispatcher'));
        }
    }

    /**
     * 注册基础组件
     *
     * @throws Exception
     */
    private function registerBaseComponent(): void
    {
        $this->bind('EventDispatcher', function(){
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });

        $this->bind('Cookie', function(){
            return new \GuzzleHttp\Cookie\FileCookieJar(__DIR__.'/cookie');
        });
    }
}