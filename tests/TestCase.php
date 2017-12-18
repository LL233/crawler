<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseCase;

class TestCase extends BaseCase
{
    protected $app;

    public function __construct()
    {
        parent::__construct();

        $this->app = \Crawler\Container\Container::getInstance();

        $register = new \Crawler\Container\RegisterComponents();
        $register->register();
    }
}
