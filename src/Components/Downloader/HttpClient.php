<?php

namespace Crawler\Components\Downloader;

use Crawler\Components\Downloader\DownloaderInterface;

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
     * @param  string $method 请求方法
     * @param  string $params 请求的其余参数
     * @return mixed 请求后获得的内容
     */
    public function download($link, $method, $params = [])
    {
        return $this->guzzleHttpClient->request($method, $link);
    }
}