<?php

namespace Tests\Crawler\Components\MultiProcess;

use Tests\TestCase;

class MainProcessTest extends TestCase
{
    public function testStart()
    {
        $process = $this->container->make('MultiProcess', [
            'taskConfig' => [
                'taskA' => [
                    "count" => 4,
                    "handle" => function(){
                        while(true) {
                            echo "hello world\n";

                            usleep(1000);
                        }
                    }
                ]
            ],
            'isDaemonize' => true,
        ]);
    }
}