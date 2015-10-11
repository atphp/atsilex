Console
====

### Start console

    php public/index.php

### Start PHP REPL

    php public/index.php at:shell

    @silex > $app;
    @silex > exit; // or control-D

### Run a PHP file

This command is useful we we need run some code quickly without writing any command or controller:

    php public/index.php at:scr /path/to/debug.php

    # @file /path/to/debug.php
    return function(atsilex\module\system\ModularApp $app) {
        dump($app->keys());
    }
