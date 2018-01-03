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
    private $defaultTag = 'defalut';

    /**
     * 当前的tag
     *
     * @var string
     */
    private $currentTag = '';

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
    public function match(string $link): LinkTagInterface
    {
        //如果当前链接没有标识
        if (empty($this->currentTag)) {
            //根据规则匹配出标识
            foreach ($this->ruleGroup as $k => $rule) {
                if (preg_match($rule, $link)) {
                    $this->currentTag = $this->tagGroup[$k];
                }
            }

            //如果所有规则都没有匹配到，则返回默认的标识
            if (empty($this->currentTag)) {
                $this->currentTag = $this->defaultTag;
            }
        }

        return $this;
    }

    /**
     * 获取当前标识
     *
     * @return string
     * @throws Exception
     */
    public function getTag(): string
    {
        if (empty($this->currentTag)) {
            throw new Exception('do not match before get');
        }

        return $this->currentTag;
    }

    /**
     * 清除标识
     */
    public function clean(): void
    {
        $this->currentTag = '';
    }
}