<?php

namespace Crawler\Components\Spider;

use Symfony\Component\EventDispatcher\Event;

/**
 * 爬虫事件对象
 * 当爬虫内的事件被触发时该事件对象将被传递
 *
 * @author LL
 */
class SpiderEvent extends Event implements \ArrayAccess
{
    /**
     * 爬虫实例
     *
     * @var SpiderInterface
     */
    private $spider;

    /**
     * 事件参数
     *
     * @var array
     */
    private $params = [];

    public function __construct(SpiderInterface $spider, $params = [])
    {
        $this->spider = $spider;
        $this->params = $params;
    }

    /**
     * 设置一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @param  mixed $value  值
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->params[$offset] = $value;
    }

    /**
     * 删除一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->params[$offset]);
    }

    /**
     * 获取一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->params[$offset] ?? null;
    }

    /**
     * 检查一个偏移位置是否存在
     *
     * @param  mixed $offset 偏移位置
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->params[$offset]);
    }

    /**
     * 魔术方法__get的实现
     * 将获取属性映射到params数组
     *
     * @param  mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->params[$name];
    }

    /**
     * 魔术方法__set的实现
     * 将设置属性映射到params数组
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }
}