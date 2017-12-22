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
        $tag = Container::getInstance()->make('Spider');

        list($method, $params) = $this->getRequestConfig($tag);

        return $this->guzzleHttpClient->request($method, $link, $params);
    }

    /**
     * 根据tag获取请求配置
     *
     * @param  string $tag
     * @return array [method => '', params => []]
     */
    private function getRequestConfig($tag)
    {
        $config = $this->requestConfig[$tag] ?? [];

        $config['method'] = $config['method'] ?? 'GET';
        $config['params'] = $config['params'] ?? [];

        return $config;
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
}