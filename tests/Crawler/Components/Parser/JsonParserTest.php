<?php

namespace Tests\Crawler\Components\Parser;

use Tests\TestCase;

class JsonParserTest extends TestCase
{
    private $parser;

    public function init()
    {
        $this->parser = $this->container->make('JsonParser');
    }

    public function testParse()
    {
        $this->parser->setContent(json_encode(['a', 'b', 'c']));

        $res = $this->parser->parseContent();

        $this->assertInternalType('array', $res);
        $this->assertCount(3, $res);
        $this->assertEquals(['a', 'b', 'c'], $res);
    }
}