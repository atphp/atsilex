<?php

namespace atsilex\module\system\tests;

use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use atsilex\module\system\tests\fixtures\modules\foo\FooModule;
use Doctrine\ORM\Tools\SchemaTool;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    private $app;

    protected function getApplicationConfiguration()
    {
        return [
            'debug'               => true,
            'app.root'            => __DIR__ . '/fixtures',
            'security.firewalls'  => [
                'default' => [
                    'pattern'   => '^/',
                    'anonymous' => '~'
                ]
            ],
            'twig.path'           => __DIR__ . '/fixtures/views',
            'twig.form.templates' => ['bootstrap_3_horizontal_layout.html.twig'],
            'session.test'        => true,
        ];
    }

    /**
     * @return ModularApp
     */
    protected function getApplication()
    {
        global $loader;

        if (null === $this->app) {
            $this->app = new ModularApp($this->getApplicationConfiguration());
            $this->app->setClassLoader($loader);
            $this->app->registerModule(new FooModule());
            $this->app->registerModule(new SystemModule());
            $this->app->boot();
        }

        return $this->app;
    }

    protected function getEntityManager()
    {
        static $ran = false;

        $em = $this->getApplication()->getEntityManager();

        if ((false === $ran) && ($ran = true)) {
            $schema = new SchemaTool($em);
            $meta = $em->getMetadataFactory()->getAllMetadata();
            $schema->dropSchema($meta); # drop all schemas
            $schema->createSchema($meta); # recreate schemas
        }

        return $em;
    }

}
