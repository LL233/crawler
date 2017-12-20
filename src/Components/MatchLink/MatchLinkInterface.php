<?php

namespace Crawler\Components\MatchLink;

/**
 * 链接匹配接口
 * 根据链接和匹配规则获取一个标识
 *
 * @author LL
 */
interface MatchLinkInterface
{
    /**
     * 设置链接匹配规则
     *
     * @param string $tag
     * @param mixed  $rule
     */
    public function setRule(string $tag, $rule): void;

    /**
     * 根据链接进行匹配
     *
     * @param  string $link
     * @return mixed
     */
    public function match(string $link);
}