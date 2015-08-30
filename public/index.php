<?php

/**
 * @file index.php
 *
 * Default front controller for @Silex application.
 * In most cases, we don't need toe edit this file.
 */

use atsilex\module\system\ModularApp;

// Get rid of global variables
return call_user_func(function () {
    global $loader;

    // Class loader is injected to app, can be used to load module's classes.
    $loader = $loader ?: require_once __DIR__ . '/../vendor/autoload.php';

    // APP_ROOT is useful to get app directories/files.
    !defined('APP_ROOT') && define('APP_ROOT', dirname(__DIR__));

    // app object, the core of the application.
    $config = require file_exists(APP_ROOT . '/config.php') ? APP_ROOT . '/config.php' : APP_ROOT . '/config.default.php';
    $appClass = isset($config['app.class']) ? $config['app.class'] : ModularApp::class;
    $app = new $appClass($config, $loader);
    $app->boot();

    return 'cli' === php_sapi_name() ? $app->getConsole()->run() : $app->run();
});
