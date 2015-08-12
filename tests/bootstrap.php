<?php

// prevent CLI app silent error
ini_set('display_errors', 1);
error_reporting(-1);

// Path to app root.
define('APP_ROOT', dirname(__DIR__) . '/');

// User composer's class loader
require_once __DIR__ . '/../vendor/autoload.php';
