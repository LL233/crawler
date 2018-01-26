<?php

namespace Crawler\Components\Parser;

/**
 * 解析器接口
 * 所有的解析器都必须实现这个接口
 * 规定了解析器的基本操作
 *
 * @author LL
 */
interface ParserInterface
{       
    /**
     * 设置解析器要解析的内容
     *
     * @param  string $content 待解析的内容
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * 根据规则解析内容，并返回解析后的内容
     *
     * @param  mixed $rule 解析的规则
     * @return array 执行解析后的内容
     */
    public function parseContent($rule): array;
}