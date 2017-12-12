<?php

namespace Crawler\Container;

use Closure;
use Exception;

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
    public static $instance;

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
    public function bind($abstract, Closure $concrete, $isOnly = true)
    {   
        //如果已经绑定，则抛出异常
        if (isset($this->aliases[$abstract])) {
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
            $this->instances[$abstract] = $instances
        }

        return $instances;
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

        return $concrete($this, $params);
    }
}