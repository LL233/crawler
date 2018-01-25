<?php

namespace Tests\Crawler\Components\Garbage;

use Tests\TestCase;

class MemoryGarbageTest extends TestCase
{
    private $garbage;

    protected function init()
    {
        $this->garbage = $this->container->make('Garbage');
    }

    /**
     * @dataProvider removeRepeatDataProvider
     */
    public function testRemoveRepeat(array $putData, array $repeatData)
    {
        $this->garbage->clear();

        $this->garbage->put($putData);

        $resData = $this->garbage->removeRepeat($repeatData);

        $this->assertCount(0, $resData);
    }

    public function removeRepeatDataProvider()
    {
        return [
            [['a', 'b'], ['a']],
            [['a', 'b', 'c'], ['a', 'c']]
        ];
    }
}