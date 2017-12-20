<?php

namespace Crawler\EventListener\Events;

use Symfony\Component\EventDispatcher\Event;
use Crawler\Components\Spider\SpiderInterface;

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
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    /**
     * 删除一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @return void
     */
    public function offsetUnset($offset)
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
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }
}