<?php

namespace vendor_name\project_name\scripts;

use vendor_name\project_name\App;

abstract class BaseScript
{

    /** @var  App */
    protected $app;

    public function __construct(App $app = null)
    {
        if (!$this->app = $app) {
            !defined('APP_CLI') && define('APP_CLI', true);
            $this->app = require __DIR__ . '/../../public/index.php';
        }
    }

}
