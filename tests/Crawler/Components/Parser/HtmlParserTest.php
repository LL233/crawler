<?php

namespace Tests\Crawler\Components\Parser;

use Tests\TestCase;

class HtmlParserTest extends TestCase
{
    private $parser;

    public function init()
    {
        $this->parser = $this->container->make('HtmlParser');
    }

    public function testParseTitle()
    {
        $this->parser->setContent(file_get_contents(__DIR__ . '/test.html'));

        $titlesData = $this->parser->parseContent(function($document) {
            $titlesData = [];

            $titleDocuments = $document->find('.posts .post .content .title a');

            foreach ($titleDocuments as $title) {
                $titlesData[] = $title->text();
            }

            return $titlesData;
        });

        $this->assertInternalType('array', $titlesData);
        $this->assertCount(14, $titlesData);
    }

    public function testParseImg()
    {
        $this->parser->setContent(file_get_contents(__DIR__ . '/test.html'));

        $imgData = $this->parser->parseContent(function($document) {
            $imgData = [];

            $imgDoucuments = $document->find('.posts .post .user-info .user-avatar img');

            foreach ($imgDoucuments as $img) {
                $imgData[] = $img->src;
            }

            return $imgData;
        });

        $this->assertInternalType('array', $imgData);
        $this->assertCount(14, $imgData);
    }
}