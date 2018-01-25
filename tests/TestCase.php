<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseCase;

abstract Class TestCase extends BaseCase
{
    protected $container;

    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = \Crawler\Container\Container::getInstance();

        $this->init();
    }

    protected function init(){}
}
