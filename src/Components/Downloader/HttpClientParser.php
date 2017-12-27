<?php

namespace Crawler\Components\Downloader;

use Crawler\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Crawler\Components\Parser\ParserInterface;
use Exception;

/**
 * 基于HttpClient的解析器
 * 作为数据解析器和HttpClient的中间层
 *
 * @author LL
 */
class HttpClientParser
{
    /**
     * Response实例
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * 数据类型和对应解析器的映射
     *
     * @var array
     */
    private $parserMap = [
        'text/html' => 'HtmlParser',
        'application/json' => 'JsonParser'
    ];

    /**
     * 从响应结果中解析出数据
     *
     * @param  ResponseInterface $response
     * @return ParserInterface
     */
    public function parserResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this->resolveData();
    }

    /**
     * 获取本次请求状态码
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->response->getStatusCode();
    }

    /**
     * 解析数据，并返回对应的解析器
     *
     * @return ParserInterface
     */
    private function resolveData()
    {
        $contentType = $this->getContentType();
        $content = $this->getContent($contentType[1] ?? 'charset=utf-8');

        //加载对应数据类型的解析器
        $parser = Container::getInstance()->make($this->parserMap[$contentType[0]]);
        $parser->setContent($content);

        return $parser;
    }

    /**
     * 获取数据类型
     *
     * @return array
     * @throws Exception
     */
    private function getContentType()
    {
        //如果没有Content-Type头，则默认为text/html;charset=utf-8
        if ($this->response->hasHeader('Content-Type')) {
            $contentType = $this->response->getHeader('Content-Type');
        } else {
            $contentType = 'text/html;charset=utf-8';
        }

        $contentType = explode(';', $contentType);

        if (isset($this->parserMap[$contentType[0]])) {
            return $contentType;
        } else {
            throw new Exception("undefind a parser of the data type, data type:{$contentType[0]}");
        }
    }

    /**
     * 获取body中的内容
     * 如果不是utf-8编码，则转码
     *
     * @param  $charset
     * @return string
     */
    private function getContent($charset)
    {
        $content = $this->response->getBody()->getContents();

        $charset = explode('=', $charset);

        if (isset($charset[1]) && $charset[1] != 'utf-8') {
            return mb_convert_encoding($content, 'utf-8', $charset[1]);
        } else {
            return $content;
        }
    }
}