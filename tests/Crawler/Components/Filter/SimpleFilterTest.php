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

            //返回空，不触发后面的默认过滤规则
            return [];
        });

        $this->filter->setFilterLinkRule('testB', function(ParserInterface $parser) use ($that){
            $res = $parser->parseContent('/hello/');

            $that->assertEquals($res[0][0], 'hello');

            //返回空，不触发后面的默认过滤规则
            return [];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('hello world');

        $this->filter->filterLink('testB', $parser);
        $this->filter->filterLink('testA', $parser);
    }

    public function testSetFilterDataRule()
    {
        $that = $this;

        $this->filter->setFilterDataRule('testA', function(ParserInterface $parser) use ($that) {
            $res = $parser->parseContent('/hello/');

            $that->assertEquals($res[0][0], 'hello');

            return [];
        });

        $this->filter->setFilterDataRule('testB', function(ParserInterface $parser) use ($that) {
            $res = $parser->parseContent('/world/');

            $that->assertEquals($res[0][0], 'world');

            return [];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('hello world');

        $this->filter->filterData('testA', $parser);
        $this->filter->filterData('testB', $parser);
    }

    public function testFilterLink()
    {
        $that = $this;

        $this->filter->setFilterLinkRule('testA', function(ParserInterface $parser) use ($that) {
            $res = $parser->parseContent('/http\:\/\/.*/');

            return [$res[0][0]];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('helloworldaaadddttteeeehttp://www.baidu.com');

        $res = $this->filter->filterLink('testA', $parser);

        $this->assertInternalType('array', $res);
        $this->assertEquals($res[0], 'http://www.baidu.com');
    }

    public function testFilterData()
    {
        $this->filter->setFilterDataRule('testA', function(ParserInterface $parser){
            $res = $parser->parseContent('/helloworld/');

            return [$res[0][0]];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('helloworldaaaccdduuhahahwwwddccxx');

        $res = $this->filter->filterData('testA', $parser);

        $this->assertInternalType('array', $res);
        $this->assertEquals($res[0], 'helloworld');
    }

    public function testDomainFilter()
    {
        $that = $this;

        //这里设定爬虫去抓取一个页面，用于设置spider的currentLink
        $spider = $this->container->make('Spider');
        $spider->getContent('https://www.baidu.com/s?wd=bilibili&rsv_spt=1&rsv_iqid=0xd6aa986900000986&issp=1&f=8&rsv_bp=0&rsv_idx=2&ie=utf-8&tn=baiduhome_pg&rsv_enter=1&rsv_sug3=9&rsv_sug1=6&rsv_sug7=100&rsv_t=c90eosZtlasxjw077Ae8Nx8haoRLwsyVbn%2F8cNNeW%2BgomkUmHKNNW8lgBdDCa9QhYC7g&rsv_sug2=0&inputT=1839&rsv_sug4=3257');

        $config = $this->container->make('Config');
        //启用域名限制
        $config['filter'] = [
            'inDomain' => true
        ];
        $config['domains'] = [
            'www.baidu.com'
        ];

        $this->filter->setFilterLinkRule('testA', function(ParserInterface $parser) use ($that) {
            $res = $parser->parseContent('/href=\"(?P<domain>[^\s]+)\"/');

            $that->assertCount(4, $res['domain']);

            return $res['domain'];
        });

        $parser = $this->container->make('RegexParser');
        $parser->setContent('<a href="http://www.baidu.com/helloworld"></a><a href="http://www.bilibili.com/helloworld"></a><a href="/newworld"></a><a href="newworld"></a>');

        $res = $this->filter->filterLink('testA', $parser);

        $this->assertCount(2, $res);
        $this->assertEquals($res[0], 'http://www.baidu.com/helloworld');
        $this->assertEquals($res[1], 'https://www.baidu.com/newworld');
    }
}