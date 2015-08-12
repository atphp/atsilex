<?php

namespace atsilex\module\queue\services;

use Bernard\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Consumer extends \Bernard\Consumer
{

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

}
