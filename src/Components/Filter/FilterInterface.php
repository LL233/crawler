<?php

namespace Crawler\Components\Filter;

use Closure;

/**
 * 过滤器接口
 * 依赖于爬虫的过滤器，抽象为主要需要过滤出两种数组
 * 一个链接的数据，用于之后的爬取
 * 一个业务所需的数据，提供给用户使用
 *
 * @author LL
 */
interface FilterInterface
{
    /**
     * 设置链接过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterLinkRule(string $tag, Closure $rule): void;

    /**
     * 设置数据过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterDataRule(string $tag, Closure $rule): void;

    /**
     * 过滤出链接数据，用于后面的爬取
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterLink(string $tag, $data);

    /**
     * 过滤出业务所需数组，提供给用户使用
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterData(string $tag, $data);
}