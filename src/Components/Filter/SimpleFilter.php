<?php

namespace Crawler\Components\Filter;

use Closure;
use Exception;

/**
 * 简单的过滤器实例
 *
 * @author LL
 */
class SimpleFilter implements FilterInterface
{
    /**
     * 链接过滤数组
     *
     * @var array
     */
    private $linkRuleGroup = [];

    /**
     * 数据过滤数组
     *
     * @var array
     */
    private $dataRuleGroup = [];

    /**
     * 设置链接过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterLinkRule(string $tag, Closure $rule): void
    {
        //设置链接过滤规则，并将闭包的$this作用域绑定到当前对象
        $this->linkRuleGroup[$tag] = $rule->call($this);
    }

    /**
     * 设置数据过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterDataRule(string $tag, Closure $rule): void
    {
        //设置链接过滤规则，并将闭包的$this作用域绑定到当前对象
        $this->dataRuleGroup[$tag] = $rule->call($this);
    }

    /**
     * 过滤出链接数据，用于后面的爬取
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterLink(string $tag, $data)
    {
        if (isset($this->linkRuleGroup[$tag])) {
            return $this->linkRuleGroup[$tag]($data);
        } else {
            throw new Exception("undefind filter link tag {$tag}");
        }
    }

    /**
     * 过滤出业务所需数组，提供给用户使用
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterData(string $tag, $data)
    {
        if (isset($this->dataRuleGroup[$tag])) {
            return $this->dataRuleGroup[$tag]($data);
        } else {
            throw new Exception("undefind filter data tag {$tag}");
        }
    }
}