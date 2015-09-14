<?php

namespace atsilex\module;

use atsilex\module\system\ModularApp;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Api\EventListenerProviderInterface;

/**
 * A module can:
 *
 * - Provide/override services.
 * - Listen to system event's
 * - Provide new routes.
 */
interface ModuleInterface extends ServiceProviderInterface,
    BootableProviderInterface,
    EventListenerProviderInterface,
    ControllerProviderInterface
{
    /**
     * @return string
     */
    public function getMachineName();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * Get module's PHP namespace.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Get path to module directory.
     *
     * @return string
     */
    public function getPath();
}
