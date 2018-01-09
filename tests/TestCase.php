<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseCase;

class TestCase extends BaseCase
{
    protected $container;

    public function __construct()
    {
        parent::__construct();

        $this->container = \Crawler\Container\Container::getInstance();
    }
}
