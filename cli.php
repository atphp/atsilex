<?php

return call_user_func(function () {
    ini_set('display_errors', 1); # prevent CLI app silent error
    error_reporting(-1);

    define('APP_CLI', true);
    $app = require __DIR__ . '/public/index.php';
    $app->boot();

    return $app->getConsole();
})->run();
