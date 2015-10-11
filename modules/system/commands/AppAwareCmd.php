<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;

abstract class AppAwareCmd extends BaseCmd
{
    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        return parent::__construct();
    }

    public function getApp()
    {
        return $this->app;
    }
}
