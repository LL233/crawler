<?php

namespace Crawler\Components\Parser;

/**
 * json数据的解析器
 *
 * @author LL
 */
class JsonParser implements ParserInterface
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
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * 将json字符串解析为数组
     *
     * @param  bool $assoc 解析的规则
     * @return array
     */
    public function parseContent($assoc): array
    {
        return json_decode($this->content, $assoc);
    }
}