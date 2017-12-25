<?php

namespace Crawler\Components\Downloader;

use Crawler\Container\Container;

/**
 * 通过http实现下载器
 * 依赖于GuzzleHttp实现
 *
 * @author LL
 */
class HttpClient implements DownloaderInterface
{
    /**
     * guzzle提供的客户端
     *
     * @var \GuzzleHttp\Client
     */
    private $guzzleHttpClient;

    /**
     * 保存请求配置的数组
     *
     * @var array
     */
    private $requestConfig = [];

    /**
     * 请求时的参数
     *
     * @var array
     */
    private $requestParams = [];

    /**
     * 请求时的方法
     *
     * @var string
     */
    private $requestMethod = 'GET';

    /**
     * 构造函数
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->guzzleHttpClient = $client;
    }

    /**
     * 对一个连接发起请求，并获得连接的内容
     *
     * @param  string $link   请求连接
     * @return mixed 请求后获得的内容
     */
    public function download($link)
    {
        $tag = Container::getInstance()->make('Spider')->getTag();

        $this->getRequestConfig($tag);

        return $this->guzzleHttpClient->request($this->requestMethod, $link, $this->requestParams);
    }

    /**
     * 根据tag获取请求配置
     *
     * @param  string $tag
     */
    private function getRequestConfig(string $tag): void
    {
        $config = $this->requestConfig[$tag] ?? [];

        $this->requestMethod = $config['method'] ?? 'GET';
        $this->requestParams = $config['params'] ?? [];
    }

    /**
     * 设置请求配置
     *
     * @param string $tag
     * @param array  $config
     */
    public function setRequestConfig(string $tag, array $config): void
    {
        $this->requestConfig[$tag] = $config;
    }

    /**
     * 设置cookie
     */
    private function setCookie(): void
    {
        if (!isset($this->requestParams['cookies'])) {
            $this->requestParams['cookies'] = Container::getInstance()->make('FileCookie');
        }
    }

    /**
     * 伪造成客户端
     */
    private function forgeClient(): void
    {
        if (!isset($this->requestParams['headers']['User-Agent'])) {
            $this->requestParams['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11';
        }
    }
}