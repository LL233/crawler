<?php

namespace Crawler\Components\LinkTag;

/**
 * 链接标识接口
 * 这个类用来匹配与当前链接符合的标识
 * 存储并返回这个标识给依赖它的对象使用
 * 当链接改变时应当清除已匹配出的标识
 *
 * @author LL
 */
interface LinkTagInterface
{
    /**
     * 设置链接匹配标识的规则
     *
     * @param string $tag  标识名
     * @param mixed  $rule 匹配规则
     */
    public function setRule(string $tag, $rule): void;

    /**
     * 根据当前链接匹配出标识
     *
     * @param  string $link
     * @return string
     */
    public function match(string $link): string;
}