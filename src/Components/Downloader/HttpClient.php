<?php

namespace Crawler\Components\Downloader;

use Crawler\Container\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Crawler\EventTag;
use Crawler\Components\Parser\ParserInterface;
use Exception;

/**
 * 通过http实现下载器
 * 依赖于GuzzleHttp实现
 *
 * @author LL
 */
class HttpClient implements DownloaderInterface
{
    /**
     * 请求时的参数
     *
     * @var array
     */
    public $requestParams = [];

    /**
     * 请求时的方法
     *
     * @var string
     */
    public $requestMethod = 'GET';

    /**
     * 请求的链接
     *
     * @var string
     */
    public $requestLink = '';

    /**
     * guzzle提供的客户端
     *
     * @var \GuzzleHttp\Client
     */
    private $guzzleHttpClient;

    /**
     * 事件派发
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * HttpClient解析器
     *
     * @var HttpClientParser
     */
    private $httpClientParser;

    /**
     * 构造函数
     *
     * @param \GuzzleHttp\Client $client
     * @param EventDispatcher    $eventDispatcher
     */
    public function __construct(\GuzzleHttp\Client $client, EventDispatcher $eventDispatcher, HttpClientParser $httpClientParser)
    {
        $this->guzzleHttpClient = $client;
        $this->eventDispatcher = $eventDispatcher;
        $this->httpClientParser = $httpClientParser;
    }

    /**
     * 对一个连接发起请求，并获得连接的内容
     *
     * @param  string $link   请求连接
     * @param  string $method 请求方法
     * @param  array  $params 请求参数
     * @return mixed 请求后获得的内容
     */
    public function download(string $link, string $method = 'GET', array $params = []): ParserInterface
    {
        $this->requestParams = $params;
        $this->requestMethod = $method;
        $this->requestLink = $link;

        //触发请求前的事件
        $this->eventDispatcher->dispatch(EventTag::REQUEST_BEFORE, Container::getInstance()->make('RequestEvent', ['downloader' => $this]));

        try {
            $response = $this->guzzleHttpClient->request($this->requestMethod, $this->requestLink, $this->requestParams);

            return $this->httpClientParser->parserResponse($response);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            $this->clear();
        }
    }

    /**
     * 清除
     */
    private function clear(): void
    {
        $this->requestParams = [];
        $this->requestMethod = '';
        $this->requestLink = '';
    }
}