<?php

use vendor_name\project_name\App;

global $loader;

// Class loader is injected to app, can be used to load module's classes.
$loader = $loader ?: require_once __DIR__ . '/../vendor/autoload.php';

// Get rid of global variables
return call_user_func(function () use ($loader) {
    // APP_ROOT is useful to get app directories/files.
    !defined('APP_ROOT') && define('APP_ROOT', dirname(__DIR__));

    // app object, the core of the application.
    $configFile = APP_ROOT . '/config.php';
    if (!file_exists($configFile)) {
        $configFile = APP_ROOT . '/config.default.php';
    }

    $app = new App(require $configFile, $loader);

    // Handle user's request or return the application object.
    return defined('APP_CLI') ? $app : $app->run();
});
