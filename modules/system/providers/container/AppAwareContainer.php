<?php

namespace atsilex\module\system\providers\container;

use atsilex\module\system\ModularApp;
use Symfony\Component\DependencyInjection\Container;

abstract class AppAwareContainer extends Container
{

    /** @var ModularApp */
    private $app;

    /**
     * @param ModularApp $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if ($this->app->offsetExists($id)) {
            return $this->app->offsetGet($id);
        }

        return parent::get($id, $invalidBehavior);
    }

}
