<?php

namespace Crawler\Components\Filter;

use Closure;
use Crawler\Container\Container;
use Crawler\Components\Parser\ParserInterface;

/**
 * 简单的过滤器实例
 *
 * @author LL
 */
class SimpleFilter implements FilterInterface
{
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
     * 配置数组
     *
     * @var array
     */
    private $config;

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

    /**
     * 默认执行的链接过滤方法
     *
     * @var array
     */
    private $baseFilterLinkMap = [
        "domainFilter",
        "noLinkFilter"
    ];

    public function __construct(string $defaultTag)
    {
        $this->defaultTag = $defaultTag;

        $this->config = Container::getInstance()->make('Config');
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
        $this->linkRuleGroup[$tag] = $rule->bindTo($this);
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
        $this->dataRuleGroup[$tag] = $rule->bindTo($this);
    }

    /**
     * 过滤出链接数据，用于后面的爬取
     *
     * @param  string          $tag
     * @param  ParserInterface $parser
     * @return array
     */
    public function filterLink(string $tag, ParserInterface $parser): array
    {
        if (isset($this->linkRuleGroup[$tag])) {
            $resData = $this->linkRuleGroup[$tag]($parser);

            //如果数据不为空，则执行默认的过滤方法，清洗掉无效链接
            if (!empty($resData)) {
                $resData = $this->baseFilterLink($resData);
            }

            //如果不为空，则重置数组key，如果为空，则直接返回空
            return !empty($resData) ? array_values($resData) : [];
        } else {
            //如果没有设置过滤规则，则返回一个空数组
            return [];
        }
    }

    /**
     * 过滤出业务所需数据，提供给用户使用
     *
     * @param  string          $tag
     * @param  ParserInterface $parser
     * @return array
     */
    public function filterData(string $tag, ParserInterface $parser): array
    {
        if (isset($this->dataRuleGroup[$tag])) {
            return $this->dataRuleGroup[$tag]($parser);
        } else {
            return [];
        }
    }

    /**
     * 基础链接过滤方法
     * 在用户获取到链接后，执行基础过滤
     * 清洗掉无效的链接
     *
     * @param  array $data
     * @return array
     */
    private function baseFilterLink(array $data): array
    {
        foreach ($this->baseFilterLinkMap as $filter) {
            $data = call_user_func([$this, $filter], $data);
        }

        return $data;
    }

    /**
     * 过滤非当前域名下的链接
     *
     * @param  array $data
     * @return array
     */
    private function domainFilter(array $data): array
    {
        if (!($this->config['filter']['inDomain'] ?? false)) {
            return $data;
        }

        $domains = $this->config['domains'];

        foreach ($data as $k=>$v) {
            $isDelete = true;

            foreach ($domains as $domain) {
                //匹配链接是否包含域名
                if (preg_match('/^(http|https|HTTP|HTTPS):\/\/(' . $domain . ')\/[^\s]+$/', $v)) {
                    $isDelete = false;
                }
            }

            //匹配链接是否是相对路径
            if (preg_match('/^\/[^\s]+$/', $v)) {
                //如果已经被标记为无法获取域名，则删除该链接
                if (!$this->unknowDomain) {
                    //如果拼接域名成功，则保留该链接，否则删除
                    if (($link = $this->stitchingLink($v)) != false) {
                        $isDelete = false;
                        $data[$k] = $link;
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

        preg_match('/^(?P<domain>(http|https|HTTP|HTTPS)\:\/\/[^\s^\/]+)\/[^\s]+$/', $currentLink, $matchData);

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
            if (!preg_match('/^(http|https|HTTP|HTTPS):\/\/[^s]+$/', $v)) {
                unset($data[$k]);
            }
        }

        return $data;
    }
}