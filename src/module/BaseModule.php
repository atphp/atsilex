<?php

namespace atsilex\module;

use atsilex\module\system\ModularApp;
use Pimple\Container;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Basic class implements ModuleInterface, provides no magic.
 */
abstract class BaseModule implements ModuleInterface
{
    /** @var string */
    protected $machineName = '';

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $version = ModularApp::VERSION;

    /** @var string */
    protected $description = '';

    /** @var string[] List of module-dependencies classes. */
    protected $requires = [];

    /** @var string */
    protected $routePrefix = '/';

    /** @var string Path to module directory */
    protected $path;

    public function __construct()
    {
        if (!$this->getMachineName()) {
            throw new \RuntimeException(sprintf('Machine name is required: %s', static::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        // Final module can override this method to define services.
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $route */
        $route = $app['controllers_factory'];

        // Final module can override this method to define its routes.
        // Example:
        // -------
        // $route->get('/hello/{name}', function($name) use ($app) {
        //      return "Hello " . $app->escape($name);
        // });

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(Container $container, EventDispatcherInterface $dispatcher)
    {
        // Final module can override this method to add event listener/subscriber.
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        // Final module can override this method to execute custom logic on app boot time.
    }

    /**
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @param string $prefix
     * @return self
     */
    public function setRoutePrefix($prefix)
    {
        $this->routePrefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getMachineName()
    {
        return $this->machineName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string[]
     */
    public function getRequires()
    {
        return $this->requires;
    }

    /**
     * Get module namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return substr(static::class, 0, strrpos(static::class, '\\'));
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->path = (new \ReflectionClass(static::class))->getFileName();
            $this->path = dirname($this->path);
        }

        return $this->path;
    }
}
