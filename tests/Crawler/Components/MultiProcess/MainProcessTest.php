<?php

namespace Tests\Crawler\Components\MultiProcess;

use Tests\TestCase;

class MainProcessTest extends TestCase
{
    public function testStart()
    {
        $process = $this->app->make('MultiProcess', [
            'handler' => function() {
                return 'hello world';
            },
            'subProcessCount' => 2
        ]);
    }
}