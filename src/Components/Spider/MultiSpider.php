<?php

namespace Crawler\Components\Spider;

use Crawler\Components\Filter\FilterInterface;
use Crawler\Components\Downloader\DownloaderInterface;
use Crawler\Components\LinkTag\LinkTagInterface;
use Crawler\Components\LinkManager\LinkManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Crawler\EventTag;
use Crawler\Container\Container;
use Crawler\Components\Parser\ParserInterface;
use Exception;

/**
 * MultiSpider抓取引擎
 *
 * @author LL
 */
class MultiSpider implements SpiderInterface
{
    /**
     * 下载器
     *
     * @var DownloaderInterface
     */
    private $downloader;

    /**
     * 过滤器
     *
     * @var FilterInterface
     */
    private $filter;

    /**
     * 队列
     *
     * @var LinkManagerInterface
     */
    private $linkManager;

    /**
     * 链接匹配
     *
     * @var LinkTagInterface
     */
    private $linkTag;

    /**
     * 事件派发
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * 当前链接对应标识
     *
     * @var mixed
     */
    private $tag;

    /**
     * 当前正在处理的链接
     *
     * @var string
     */
    public $currentLink = '';

    public function __construct(
        DownloaderInterface $downloader,
        LinkManagerInterface $linkManager,
        FilterInterface $filter,
        LinkTagInterface $linkTag,
        EventDispatcher $event
    ) {
        $this->downloader = $downloader;
        $this->linkManager = $linkManager;
        $this->filter = $filter;
        $this->linkTag = $linkTag;
        $this->eventDispatcher = $event;
    }

    /**
     * 获取抓取内容
     *
     * @param  string $link
     * @return ParserInterface
     *
     * @throws Exception
     */
    public function getContent(string $link): ParserInterface
    {
        //设置当前链接
        $this->setCurrentLink($link);

        $parser = $this->downloader->download($link);

        if ($parser) {
            $this->dispatch(EventTag::SPIDER_GET_CONTENT_AFTER, ["parser" => $parser]);

            return $parser;
        } else {
            throw new Exception('not has data');
        }
    }

    /**
     * 清洗数据
     *
     * @param  ParserInterface $parser
     * @return void
     */
    public function filterData(ParserInterface $parser): void
    {
        //过滤链接和数据
        $linkRes = $this->filter->filterLink($this->tag, $parser);
        $dataRes = $this->filter->filterData($this->tag, $parser);

        $this->dispatch(EventTag::SPIDER_FILTER_CONTENT_AFTER, [
            "linkRes" => $linkRes,
            "dataRes" => $dataRes
        ]);
    }

    /**
     * 准备下一次的抓取
     *
     * @return string
     */
    public function next(): string
    {
        $this->matchLink = '';
        $this->tag = '';

        //获取下一个链接
        $nextLink = $this->linkManager->getLink();

        $this->dispatch(EventTag::SPIDER_NEXT_LINK_AFTER, ["nextLink" => $nextLink]);

        return $nextLink;
    }

    /**
     * 抓取结束
     *
     * @return void
     */
    public function end(): void
    {
        //爬虫停止事件
        $this->dispatch(EventTag::SPIDER_STOP);

        exit(0);
    }

    /**
     * 获取当前链接所对应的tag
     *
     * @param string $link
     */
    private function setCurrentLink(string $link): void
    {
        $this->currentLink = $link;
        $this->tag = $this->linkTag->match($this->currentLink)->getTag();
    }

    /**
     * 事件派发
     *
     * @param string $eventTag 事件名称
     * @param array  $params   事件参数
     */
    private function dispatch(string $eventTag, array $params = []): void
    {
        $this->eventDispatcher->dispatch($eventTag, Container::getInstance()->make('SpiderEvent', [
            "spider" => $this,
            "params" => $params
        ]));
    }
}