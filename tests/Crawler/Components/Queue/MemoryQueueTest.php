<?php

namespace Tests\Crawler\Components\Queue;

use Tests\TestCase;

class MemoryQueueTest extends TestCase
{
    private $queue;

    public function init()
    {
        $this->queue = $this->container->make('Queue');
    }

    public function testPushOut()
    {
        $this->queue->push(['abc']);
        $this->queue->push(['efg']);

        $this->assertEquals('abc', $this->queue->out());
    }

    public function testHas()
    {
        $this->queue->push(['LL']);

        $this->assertTrue($this->queue->has('LL'));
        $this->assertNotTrue($this->queue->has('rrr'));
    }

    public function testIsEmptyClear()
    {
        $this->queue->push(['abc']);

        $this->assertNotTrue($this->queue->isEmpty());

        $this->queue->clear();

        $this->assertTrue($this->queue->isEmpty());
    }

    public function removeRepeatDataProvider()
    {
        return [
            [['a', 'b', 'c'], ['a', 'd'], 1],
            [['a', 'b', 'c'], ['d', 'e', 'f'], 3]
        ];
    }

    /**
     * @dataProvider removeRepeatDataProvider
     */
    public function testRemoveRepeat(array $putData, array $removeData, int $assertCount)
    {
        $this->queue->push($putData);

        $resData = $this->queue->removeRepeat($removeData);

        $this->assertCount($assertCount, $resData);
    }

    /**
     * @before
     * @after
     */
    public function clear()
    {
        $this->queue->clear();
    }
}