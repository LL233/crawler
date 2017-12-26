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

    /**
     * 获取爬虫当前的标识
     * 这个标识可以是任意类型
     * 但一定要能标记出爬虫当前爬取的链接
     *
     * @return mixed
     */
    public function getTag();
}