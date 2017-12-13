<?php

namespace Crawler\Components\SpiderController;

/**
 * 爬虫控制器接口
 * 负责启动停止爬虫
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