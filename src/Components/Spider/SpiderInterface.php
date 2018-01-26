<?php

namespace Crawler\Components\Spider;

use Crawler\Components\Parser\ParserInterface;

/**
 * 爬虫接口
 *
 * @author LL
 */
interface SpiderInterface
{
    /**
     * 获取抓取内容
     *
     * @param  string $link
     * @return ParserInterface
     */
    public function getContent(string $link): ParserInterface;

    /**
     * 清洗数据
     *
     * @param  ParserInterface $parser
     * @return void
     */
    public function filterData(ParserInterface $parser): void;

    /**
     * 准备下一次的抓取
     *
     * @return string
     */
    public function next(): string;

    /**
     * 抓取结束
     *
     * @return void
     */
    public function end(): void;
}