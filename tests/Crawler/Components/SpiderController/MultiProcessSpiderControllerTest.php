<?php

namespace Tests\Crawler\Components\SpiderController;

use Tests\TestCase;

class MultiSpiderControllerTest extends TestCase
{
    public function testStart()
    {
        $config = $this->container->make('Config', ["config" => ["startLink" => 'https://www.baidu.com']]);

        $spiderController = $this->container->make("SpiderController");

        $spiderController->start();
    }
}