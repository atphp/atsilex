<?php

namespace atsilex\module\system\events;

use atsilex\module\system\ModularApp;
use Symfony\Component\EventDispatcher\GenericEvent;

class AppEvent extends GenericEvent
{
    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app, $subject = null, array $arguments = array())
    {
        $this->app = $app;

        return parent::__construct($subject, $arguments);
    }

    /**
     * @return ModularApp
     */
    public function getApp()
    {
        return $this->app;
    }
}
