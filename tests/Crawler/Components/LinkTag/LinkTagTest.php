<?php

namespace Tests\Crawler\Components\LinkTag;

use Tests\TestCase;

class LinkTagTest extends TestCase
{
    private $linkTag;

    public function init()
    {
        $this->linkTag = $this->container->make('LinkTag');
    }

    /**
     * @dataProvider matchDataProvider
     */
    public function testMatch(string $tag, string $rule, string $link, string $assertTag)
    {
        if (!empty($tag) && !empty($rule)) {
            $this->linkTag->setRule($tag, $rule);
        }

        $this->assertEquals($assertTag, $this->linkTag->match($link));
    }

    public function matchDataProvider()
    {
        return [
            ['testA', '/^http:\/\/www.baidu.com\/testA$/', 'http://www.baidu.com/testA', 'testA'],
            ['', '', 'http://www.baidu.com/testA', 'testA'],
            ['testB', '/^http:\/\/www.baidu.com\/testB$/', 'http://www.baidu.com/testB', 'testB'],
            ['testC', '/^http:\/\/www.baidu.com\/testC$/', 'http://www.baidu.com/testE', 'default'],
        ];
    }
}