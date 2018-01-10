<?php

namespace Tests\Crawler\Components\Spider;

use Tests\TestCase;

/**
 * 多进程爬虫单页测试
 *
 * @author LL
 */
class MultiSpiderTest extends TestCase
{
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
}