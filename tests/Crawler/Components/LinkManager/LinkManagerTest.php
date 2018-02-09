<?php

namespace Tests\Crawler\Components\LinkManager;

use Tests\TestCase;

class LinkManagerTest extends TestCase
{
    private $linkManager;

    public function init()
    {
        $this->container->make('Config')['garbageClearMax'] = 3;
        $this->linkManager = $this->container->make('LinkManager');
    }

    public function testGetLink()
    {
        $emptyRes = $this->linkManager->getLink();
        $this->assertEmpty($emptyRes);
    }

    public function testSaveLink()
    {
        $saveLinkData = [
            'http://www.baidu.com',
            'http://www.google.com',
            'http://www.xinlang.com'
        ];

        $this->linkManager->saveLink($saveLinkData);

        $res = $this->linkManager->getLink();

        $this->assertEquals('http://www.baidu.com', $res);
    }

    /**
     * @before
     * @after
     */
    public function clear()
    {
        $this->container->make('Queue')->clear();
        $this->container->make('Garbage')->clear();
    }
}