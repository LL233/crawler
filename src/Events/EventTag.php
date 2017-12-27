<?php

namespace Crawler\Events;

/**
 * 事件名称
 */
final class EventTag
{
    //爬虫启动事件名
    const SPIDER_START = 'spider.start';
    //爬虫停止事件名
    const SPIDER_STOP = 'spider.stop';
    //爬虫获取内容后
    const SPIDER_GET_CONTENT_AFTER = 'spider.getContent.after';
    //爬虫过滤内容后
    const SPIDER_FILTER_CONTENT_AFTER = 'spider.filterContent.after';
    //爬虫获取下一个链接后
    const SPIDER_NEXT_LINK_AFTER = 'spider.nextLink.after';

    //发起请求前的事件
    const REQUEST_BEFORE = 'downloader.request.before';
}