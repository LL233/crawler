<?php

namespace Tests\Crawler\Components\ConfigSetting;

use Tests\TestCase;

class ConfigSettingTest extends TestCase
{
    private $config;

    public function __construct()
    {
        parent::__construct();

        $this->config = $this->app->make('Config', [
            'config' => [
                'host' => '127.0.0.1',
                'name' => 'liulu',
                'email' => '2504767240@qq.com'
            ]
        ]);
    }

    public function testGet()
    {
        $this->assertEquals($this->config['name'], 'liulu');
        $this->assertEquals($this->config['email'], '2504767240@qq.com');
    }

    public function testSet()
    {
        $this->config['name'] = 'LL';
        $this->assertEquals($this->config['name'], 'LL');
    }

    public function testIsset()
    {
        $this->assertNull($this->config['address']);
    }
}