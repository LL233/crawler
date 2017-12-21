<?php

namespace Crawler\Components\Filter;

use Closure;
use Crawler\Container\Container;

/**
 * 简单的过滤器实例
 *
 * @author LL
 */
class SimpleFilter implements FilterInterface
{
    /**
     * 默认的链接过滤规则
     *
     * @var array
     */
    private $defaultLinkRuleGroup = [];

    /**
     * 默认的数据过滤规则
     *
     * @var array
     */
    private $defaultDataRuleGroup = [];

    /**
     * 链接过滤数组
     *
     * @var array
     */
    private $linkRuleGroup = [];

    /**
     * 数据过滤数组
     *
     * @var array
     */
    private $dataRuleGroup = [];

    /**
     * 默认的tag名称
     *
     * @var string
     */
    private $defaultTag;

    /**
     * 过滤规则的相关设置
     *
     * @var array
     */
    private $filterConfig;

    /**
     * 当前的域名
     *
     * @var string
     */
    private $currentDomain;

    /**
     * 标记当前的链接不被发现
     *
     * @var bool
     */
    private $unknowDomain = false;

    public function __construct(string $defaultTag)
    {
        $this->defaultTag = $defaultTag;

        $this->filterConfig = Container::getInstance()->make('Config')['filter'];

        $this->registerDefaultLinkRule();
    }

    /**
     * 设置链接过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterLinkRule(string $tag, Closure $rule): void
    {
        //设置链接过滤规则，并将闭包的$this作用域绑定到当前对象
        $this->linkRuleGroup[$tag] = $rule->call($this);
    }

    /**
     * 设置数据过滤规则
     *
     * @param string  $tag
     * @param Closure $rule
     */
    public function setFilterDataRule(string $tag, Closure $rule): void
    {
        //设置链接过滤规则，并将闭包的$this作用域绑定到当前对象
        $this->dataRuleGroup[$tag] = $rule->call($this);
    }

    /**
     * 过滤出链接数据，用于后面的爬取
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterLink(string $tag, $data)
    {
        //如果是默认的tag名称，则不进行过滤
        if ($tag == $this->defaultTag) {
            return false;
        }

        if (isset($this->linkRuleGroup[$tag])) {
            $data = $this->defaultFilterLink($data);

            return $this->linkRuleGroup[$tag]($data);
        } else {
            return false;
        }
    }

    /**
     * 过滤出业务所需数组，提供给用户使用
     *
     * @param  string $tag
     * @param  mixed  $data
     * @return mixed
     */
    public function filterData(string $tag, $data)
    {
        if ($tag == $this->defaultTag) {
            return false;
        }

        if (isset($this->dataRuleGroup[$tag])) {
            $data = $this->defaultFilterData($data);

            return $this->dataRuleGroup[$tag]($data);
        } else {
            return false;
        }
    }

    /**
     * 注册默认的过滤规则
     *
     * @param mixed $rule 过滤规则
     */
    public function registerDefaultFilterLink($rule): void
    {
        $this->defaultLinkRuleGroup[] = $rule;
    }

    /**
     * 默认的链接过滤
     *
     * @param  array $data
     * @return array
     */
    private function defaultFilterLink(array $data): array
    {
        foreach ($this->defaultLinkRuleGroup as $rule) {
            $data = call_user_func($rule, [$data]);
        }

        return $data;
    }

    /**
     * 注册默认的数据过滤规则
     *
     * @param mixed $rule 过滤规则
     */
    public function registerDefaultFilterData($rule): void
    {
        $this->defaultDataRuleGroup[] = $rule;
    }

    /**
     * 默认的数据过滤
     *
     * @param  array $data
     * @return array
     */
    private function defaultFilterData(array $data): array
    {
        foreach ($this->defaultDataRuleGroup as $rule) {
            $data = call_user_func($rule, $data);
        }

        return $data;
    }

    /**
     * 注册默认的链接过滤规则
     */
    private function registerDefaultLinkRule(): void
    {
        $this->registerDefaultFilterLink([$this, 'noLinkFilter']);
        $this->registerDefaultFilterLink([$this, 'domainFilter']);
    }

    /**
     * 过滤非当前域名下的链接
     *
     * @param  array $data
     * @return array
     */
    private function domainFilter(array $data): array
    {
        if (!($this->filterConfig['inDomain'] ?? false)) {
            return $data;
        }

        $domains = Container::getInstance()->make('Config')['domains'];

        foreach ($data as $k=>$v) {
            $isDelete = true;

            foreach ($domains as $domain) {
                //匹配链接是否包含域名
                if (preg_match('(http|https|HTTP|HTTPS):\/\/(' . $domain . ')+\/[^\s]+', $v)) {
                    $isDelete = false;
                }
                //匹配链接是否是相对路径
                if (!preg_match('\/[^\s]+', $v)) {
                    //如果已经被标记为无法获取域名，则删除该链接
                    if (!$this->unknowDomain) {
                        //如果拼接域名成功，则保留该链接，否则删除
                        if (($link = $this->stitchingLink($v)) != false) {
                            $isDelete = false;
                            $data[$k] = $link;
                        }
                    }
                }
            }

            if ($isDelete) {
                unset($data[$k]);
            }
        }

        return $data;
    }

    /**
     * 给一个相对路径的链接加上域名
     * 域名为当前链接的域名
     *
     * @param  $link
     * @return string|bool
     */
    private function stitchingLink($link)
    {
        //如果没有当前域名，则去获取
        if (empty($this->currentDomain)) {
            //如果已经被标记为获取不到，则直接返回false
            if ($this->unknowDomain) {
                return false;
            }

            $this->setCurrentDomain();
        }

        return $this->currentDomain.$link;
    }

    /**
     * 获取当前链接的域名
     * 存在一定的可能行，无法从当前的链接中获取域名
     * 也许是正则表达式匹配的问题，也许是其它问题
     * 为了应对这种情况的发生
     * 如果出现了当前链接无法匹配获取到域名
     * 则会将从当前链接下获取到的所有其它没有域名的链接删除掉
     * 这样避免将无用链接加入到队列中
     */
    private function setCurrentDomain(): void
    {
        $currentLink = Container::getInstance()->make('Spider')->currentLink;

        $matchData = [];

        preg_match('(?P<domain>(http|https|HTTP|HTTPS)\:\/\/[^\s^\/]+)\/[^s]+', $currentLink, $matchData);

        $this->currentDomain = $matchData['domain'] ?? '';

        //如果没有匹配到域名，则标记当前链接匹配域名无效
        if (empty($this->currentDomain)) {
            $this->unknowDomain = true;
        }
    }

    /**
     * 过滤非链接的数据
     *
     * @param  array $data
     * @return array
     */
    private function noLinkFilter(array $data): array
    {
        foreach ($data as $k=>$v) {
            if (!preg_match('^(http|https|HTTP|HTTPS):\/\/[^s]+$', $v)) {
                unset($data[$k]);
            }
        }
    }
}