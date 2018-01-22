<?php

namespace Tests\Crawler\Components\Filter;

use Crawler\Components\Parser\ParserInterface;
use Tests\TestCase;

class SimpleFilter extends TestCase
{
    private $filter;

    public function __construct()
    {
        parent::__construct();

        $this->filter = $this->container->make('Filter');
    }

    public function testSetFilterLinkRule()
    {
        $that = $this;

        $this->filter->setFilterLinkRule('testA', function(ParserInterface $parser) use ($that){
            $res = $parser->parseContent('/world/');

            $that->assertEquals($res[0][0], 'world');

            return [];
        });

        $this->filter->setFilterLinkRule('testB', function(ParserInterface $parser) use ($that){
            $res = $parser->parseContent('/hello/');

            $that->assertEquals($res[0][0], 'hello');

            return [];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('hello world');

        $this->filter->filterLink('testB', $parser);
        $this->filter->filterLink('testA', $parser);
    }
}