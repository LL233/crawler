<?php

namespace Crawler\Components\Parser;

use Crawler\ComponentProvider;

/**
 * Parser组件提供者
 *
 * @author LL
 */
class ParserComponentProvider extends ComponentProvider
{
    public function register(): void
    {
        $this->registerHtmlParser();
        $this->registerJsonParser();
        $this->registerOtherParser();
    }

    /**
     * 注册HtmlParser
     */
    private function registerHtmlParser()
    {
        $this->container->bind('Document', function(){
            return new \DiDom\Document();
        });

        $this->container->bind('HtmlParser', function($container){
            return new \Crawler\Components\Parser\HtmlParser($container->make('Document'));
        });
    }

    /**
     * 注册JsonParser
     */
    private function registerJsonParser()
    {
        $this->container->bind('JsonParser', function(){
            return new \Crawler\Components\Parser\JsonParser();
        });
    }

    /**
     * 注册其余的Parser
     */
    private function registerOtherParser()
    {
        $this->container->bind('RegexParser', function(){
            return new \Crawler\Components\Parser\RegexParser();
        });
    }
}