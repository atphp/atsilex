<?php

ini_set('display_errors', 1); # prevent CLI app silent error
error_reporting(-1);

use Doctrine\DBAL\Tools\Console\ConsoleRunner as DBAL;
use Doctrine\ORM\Tools\Console\ConsoleRunner as ORM;
use Symfony\Component\Console\Application;

return call_user_func(
    function () {
        define('APP_CLI', true);
        $app = require __DIR__ . '/public/index.php';
        $app->boot();

        $console = $app->getConsole();

        // Doctrine commands
        $console->setHelperSet(DBAL::createHelperSet($app->getDb()));
        $console->setHelperSet(ORM::createHelperSet($app->getEntityManager()));
        DBAL::addCommands($console);
        ORM::addCommands($console);

        // Our custom commands
        foreach ($app->keys() as $key) {
            if (0 === strpos($key, '@')) {
                if (false !== strpos($key, '.cmd.')) {
                    $console->add($app[$key]);
                }
            }
        }

        return $console;
    }
)->run();
