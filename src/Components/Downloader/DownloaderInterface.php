<?php

namespace Crawler\Components\Downloader;

use Crawler\Components\Parser\ParserInterface;

/**
 * 下载器的接口
 * 所有下载器都必须实现这个接口
 *
 * @author LL
 */
interface DownloaderInterface
{
    /**
     * 对一个连接发起请求，并获得连接的内容
     *
     * @param  string $link   请求连接
     * @param  string $method 请求方式
     * @param  array  $params 请求的参数
     * @return ParserInterface
     */
    public function download(string $link, string $method = 'GET', array $params = []): ParserInterface;
}