<?php

namespace vendor_name\project_name\scripts;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use vendor_name\project_name\App;

abstract class BaseScript
{

    /** @var  App */
    protected $app;

    public function __construct(App $app = null)
    {
        $this->app = $app;
        if (null === $app) {
            !defined('APP_CLI') && define('APP_CLI', true);
            $this->app = require __DIR__ . '/../../public/index.php';
        }
    }

}
