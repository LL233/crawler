<?php

namespace Crawler\Components\LinkManager;

/**
 * 链接调度接口
 * 负责获取链接和保存链接
 *
 * @author LL
 */
interface LinkManagerInterface
{
    /**
     * 获取一个链接
     * 如果没有有效链接可以返回
     * 则返回一个空字符串
     *
     * @return string
     */
    public function getLink(): string;

    /**
     * 保存链接
     *
     * @param array $link
     */
    public function saveLink(array $link): void;
}