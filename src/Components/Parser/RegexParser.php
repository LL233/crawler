<?php

namespace Crawler\Components\Parser;

/**
 * 正则表达式解析器
 * 使用正则表达式对内容进行解析
 *
 * @author LL
 */
class RegexParser implements ParserInterface
{
    /**
     * 待解析的内容
     *
     * @var string
     */
    private $content;

    /**
     * 设置解析器要解析的内容
     *
     * @param  string $content 待解析的内容
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * 根据规则解析内容，并返回解析后的内容
     *
     * @param  string $regex 正则表达式
     * @return mixed 执行解析后的内容
     */
    public function parseContent($regex)
    {
        $matches = [];

        preg_match_all($regex, $this->content, $matches);

        return $matches;
    }
}