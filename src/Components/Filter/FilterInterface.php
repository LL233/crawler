<?php

namespace Crawler\Components\Filter;

use Closure;
use Crawler\Components\Parser\ParserInterface;

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
     * @param string  $tag  链接标识
     * @param Closure $rule 一个闭包函数，参数为解析器实例
     */
    public function setFilterLinkRule(string $tag, Closure $rule): void;

    /**
     * 设置数据过滤规则
     *
     * @param string  $tag  链接标识
     * @param Closure $rule 一个闭包函数，参数为解析器实例
     */
    public function setFilterDataRule(string $tag, Closure $rule): void;

    /**
     * 过滤出链接数据，用于后面的爬取
     *
     * @param  string          $tag    当前链接的标识
     * @param  ParserInterface $parser 解析器实例
     * @return array
     */
    public function filterLink(string $tag, ParserInterface $parser): array;

    /**
     * 过滤出业务所需数据，提供给用户使用
     *
     * @param  string          $tag    当前链接的标识
     * @param  ParserInterface $parser 解析器实例
     * @return array
     */
    public function filterData(string $tag, ParserInterface $parser): array;
}