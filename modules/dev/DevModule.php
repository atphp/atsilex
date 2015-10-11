<?php

namespace atsilex\module\dev;

use atsilex\module\Module;
use Boris\Boris;
use Pimple\Container;
use Silex\Application;
use Silex\Provider\VarDumperServiceProvider;

class DevModule extends Module
{
    protected $machineName = 'dev';
    protected $name        = 'Dev';

    public function register(Container $c)
    {
        $c->register(new VarDumperServiceProvider());

        $c['shell'] = function (Container $c) {
            $shell = new Boris('@silex > ');

            $shell->setLocal([
                'app'    => $c,
                'em'     => $c->getEntityManager(),
                'mailer' => $c->getMailer(),
            ]);

            return $shell;
        };
    }
}
