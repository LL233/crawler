<?php

namespace Crawler\Components\Spider;

/**
 * 爬虫接口
 *
 * @author LL
 */
interface SpiderInterface
{
    /**
     * 获取抓取内容
     *
     * @param  mixed $link
     * @return mixed
     */
    public function getContent($link);

    /**
     * 清洗数据
     *
     * @param  mixed $data
     * @return mixed
     */
    public function filterData($data);

    /**
     * 准备下一次的抓取
     *
     * @return mixed
     */
    public function next();

    /**
     * 抓取结束
     *
     * @return mixed
     */
    public function end();
}