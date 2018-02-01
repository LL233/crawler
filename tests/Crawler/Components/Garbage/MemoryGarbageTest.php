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

    public function testPut()
    {
        $this->garbage->put(['aaa', 'bbb', 'ccc']);

        $this->assertEquals(3, $this->garbage->count());
    }

    public function testCount()
    {
        $this->garbage->put(['aaa', 'bbb', 'ccc']);

        $this->assertEquals(3, $this->garbage->count());

        $this->garbage->clear();

        $this->assertEquals(0, $this->garbage->count());
    }

    public function testHas()
    {
        $this->garbage->put(['aaa', 'bbb', 'ccc']);

        $this->assertTrue($this->garbage->has('aaa'));
        $this->assertNotTrue($this->garbage->has('ddd'));
    }

    /**
     * @dataProvider removeRepeatDataProvider
     */
    public function testRemoveRepeat(array $putData, array $repeatData)
    {
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

    /**
     * @after
     */
    public function clear()
    {
        $this->garbage->clear();
    }
}