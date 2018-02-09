<?php

namespace Tests\Crawler\Components\Parser;

use Tests\TestCase;

class RegexParserTest extends TestCase
{
    private $parser;

    public function init()
    {
        $this->parser = $this->container->make('RegexParser');
    }

    public function testParse()
    {
        $this->parser->setContent('aaabbbhelloworldcccdddaaaee');

        $res = $this->parser->parseContent('/helloworld/');

        $this->assertEquals('helloworld', $res[0][0]);
    }
}