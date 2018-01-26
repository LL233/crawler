<?php

namespace Crawler\Components\ConfigSetting;

use ArrayAccess;

/**
 * 配置组件
 * 提供全局的配置信息
 * 通过实现ArrayAccess接口，提供数组形式的访问
 *
 * @author LL
 */
class ConfigSetting implements ArrayAccess
{
    private $config = [];

    /**
     * 构造函数
     * 对配置数组进行赋值
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->config = $params;
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
        $this->config[$offset] = $value;
    }

    /**
     * 删除一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->config[$offset]);
    }

    /**
     * 获取一个偏移位置的值
     *
     * @param  mixed $offset 偏移位置
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->config[$offset] ?? null;
    }

    /**
     * 检查一个偏移位置是否存在
     *
     * @param  mixed $offset 偏移位置
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->config[$offset]);
    }
}