<?php

namespace Crawler\Components\LinkTag;

use Exception;

/**
 * 根据链接匹配出一个标识
 *
 * @author LL
 */
class LinkTag implements LinkTagInterface
{
    /**
     * 存储规则的数组
     *
     * @var array
     */
    private $ruleGroup = [];

    /**
     * 存储标识的数组
     *
     * @var array
     */
    private $tagGroup = [];

    /**
     * 默认的tag
     *
     * @var string
     */
    private $defaultTag = 'default';

    /**
     * 缓存链接匹配对应的标识
     *
     * @var array
     */
    private $cacheData = [];

    /**
     * 设置链接匹配规则
     *
     * @param string $tag
     * @param string $rule
     */
    public function setRule(string $tag, $rule): void
    {
        $this->ruleGroup[] = $rule;
        $this->tagGroup[] = $tag;
    }

    /**
     * 根据链接进行匹配
     *
     * @param  string $link
     * @return LinkTagInterface
     */
    public function match(string $link): string
    {
        //如果缓存数据不为空，并且这次匹配的链接与缓存中一致，则直接返回缓存中的标识
        if (!empty($this->cacheData) && $this->cacheData['link'] == $link) {
            return $this->cacheData['tag'];
        } else {
            $this->cacheData = [];
        }

        //默认为默认标识名称
        $currentTag = $this->defaultTag;

        //根据规则匹配出标识
        foreach ($this->ruleGroup as $k => $rule) {
            if (preg_match($rule, $link)) {
                $currentTag = $this->tagGroup[$k];
            }
        }

        //将链接对应的标识缓存起来
        $this->cacheData = [
            'tag' => $currentTag,
            'link' => $link
        ];

        return $currentTag;
    }
}