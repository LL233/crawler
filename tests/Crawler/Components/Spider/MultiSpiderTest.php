<?php

namespace Tests\Crawler\Components\Spider;

use Crawler\EventTag;
use Tests\TestCase;
use Crawler\Components\Spider\SpiderEvent;

/**
 * 多进程爬虫单页测试
 *
 * @author LL
 */
class MultiSpiderTest extends TestCase
{
    /**
     * @var \Crawler\Components\Spider\MultiSpider
     */
    private $multiSpider;

    public function __construct()
    {
        parent::__construct();

        $this->multiSpider = $this->container->make('Spider');
    }

    public function testGetContent()
    {
        $res = $this->multiSpider->getContent('http://www.baidu.com');

        $this->assertInstanceOf(\Crawler\Components\Parser\ParserInterface::class, $res);
    }

    public function testFilterData()
    {
        $parser = $this->multiSpider->getContent('http://www.baidu.com');

        $eventDispatcher = $this->container->make('EventDispatcher');

        $eventDispatcher->addListener(EventTag::SPIDER_FILTER_CONTENT_AFTER, function(SpiderEvent $event){
            $this->assertContainsOnly('array', $event->linkRes);
            $this->assertContainsOnly('array', $event->dataRes);
        });

        $this->multiSpider->filterData($parser);
    }
}