<?php

namespace Crawler\Components\Parser;

use Closure;

/**
 * html解析器
 *
 * @author LL
 */
class HtmlParser implements ParserInterface
{
    /**
     * 解析核心，提供html的dom解析
     *
     * @var \DiDom\Document
     */
    private $document;

    /**
     * 构造函数
     * 注入解析核心
     *
     * @param \DiDom\Document $document
     */
    public function __construct(\DiDom\Document $document)
    {
        $this->document = $document;
    }

    /**
     * 设置解析器要解析的内容
     *
     * @param  string $content 待解析的内容
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->document->loadHtml($content);
    }

    /**
     * 根据规则解析内容，并返回解析后的内容
     *
     * @param  Closure $parserFun 解析的规则
     * @return array 执行解析后的内容
     *
     * @throws \Exception
     */
    public function parseContent($parserFun): array
    {
        if (!$parserFun instanceof Closure) {
            throw new \Exception('HtmlParser parser must be function');
        }

        return $parserFun($this->document);
    }
}