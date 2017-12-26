<?php

namespace Crawler\Components\Downloader;

use Crawler\Components\ConfigSetting\ConfigSetting;
use Crawler\Components\Spider\SpiderInterface;
use Crawler\Container\Container;

/**
 * 使用HttpClient组件发起请求前执行的基础事件
 *
 * @author LL
 */
class HttpClientBaseEvent
{
    /**
     * HttpClient对象实例
     *
     * @var HttpClient
     */
    private $client;

    /**
     * 爬虫实例
     *
     * @var SpiderInterface
     */
    private $spider;

    /**
     * 请求的配置数组
     *
     * @var array
     */
    private $config;

    /**
     * 基础执行事件的映射
     *
     * @var array
     */
    private $baseEventMap = [
        "setRequestConfig",
        "setCookie",
        "forgeClient"
    ];

    /**
     * @param SpiderInterface $spider
     * @param ConfigSetting   $config
     */
    public function __construct(SpiderInterface $spider, ConfigSetting $configSetting)
    {
        $this->spider = $spider;
        $this->config = $configSetting['request'] ?? [];
    }

    /**
     * 执行请求前的基础事件
     *
     * @param HttpClient $client
     */
    public function baseEvent(HttpClient $client): void
    {
        $this->client = $client;

        foreach ($this->baseEventMap as $event) {
            call_user_func([$this, $event]);
        }
    }

    /**
     * 从配置中获取这次请求的配置
     */
    private function setRequestConfig(): void
    {
        //如果没有设置请求参数，则从配置中获取
        if (empty($this->client->requestParams)) {
            $this->client->requestParams = $this->config[$this->spider->getTag()] ?? [];
        }
    }

    /**
     * 设置cookie
     */
    private function setCookie(): void
    {
        //如果没有配置cookie
        if (!isset($this->client->requestParams['cookies'])) {
            $this->client->requestParams['cookies'] = Container::getInstance()->make('FileCookie');
        }
    }

    /**
     * 伪造请求客户端
     */
    private function forgeClient(): void
    {
        //如果没有设置User-Agent
        if (!isset($this->client->requestParams['headers']['User-Agent'])) {
            $this->client->requestParams['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11';
        }
    }
}