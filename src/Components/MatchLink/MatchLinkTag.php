<?php

namespace Crawler\Components\MatchLink;

/**
 * 根据链接匹配出一个标识
 *
 * @author LL
 */
class MatchLinkTag implements MatchLinkInterface
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
    private $defaultTag = 'defalut';

    /**
     * 设置链接匹配规则
     *
     * @param string $tag
     * @param string  $rule
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
     * @return string
     */
    public function match(string $link): string
    {
        foreach ($this->ruleGroup as $k=>$rule) {
            if (preg_match($rule, $link)) {
                return $this->tagGroup[$k];
            }
        }

        return $this->defaultTag;
    }

    /**
     * 获取默认的tag名称
     *
     * @return string
     */
    public function getDefaultTag(): string
    {
        return $this->defaultTag;
    }
}