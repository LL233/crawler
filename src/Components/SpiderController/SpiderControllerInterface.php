<?php

namespace Crawler\Components\SpiderController;

/**
 * 爬虫控制器接口
 * 负责启动停止爬虫
 * 并不控制爬虫内部具体的行为，例如要抓取的链接，要过滤的内容
 * 爬虫控制器只负责启动和停止一个爬虫
 * 爬虫内部具体的操作由爬虫本身控制
 *
 * @author LL
 */
interface SpiderControllerInterface
{
    /**
     * 启动爬虫
     *
     * @return void
     */
    public function start();

    /**
     * 停止爬虫
     *
     * @return void
     */
    public function stop();
}