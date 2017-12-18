<?php

namespace Tests\Crawler\Components\Downloader;

use Tests\TestCase;

class HttpClientTest extends TestCase
{
    public function testDownload()
    {
        $downloader = $this->app->make('Downloader');
        $response = $downloader->download('http://www.baidu.com', 'GET');
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotFalse((string)$response->getBody());
    }
}