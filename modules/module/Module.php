<?php

namespace atsilex\module;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * A module can:
 *
 * - Provide/override services.
 * - Listen to system event's
 * - Provide new routes.
 *
 * This is a base class for other modules to extends.
 */
abstract class Module implements ServiceProviderInterface,
    BootableProviderInterface,
    EventListenerProviderInterface,
    ControllerProviderInterface
{

    /** @var string */
    protected $routePrefix = '/';

    /** @var string */
    protected $machineName = '';

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $version = '0.1.0';

    /**
     * @TODO Need documentation.
     * @TODO Need test case.
     *
     * @var string
     */
    protected $routeFile = null;

    /** @var string Path to module directory */
    protected $path;

    /**
     * List of module-dependencies classes.
     *
     * @var string[]
     */
    protected $requires = [];

    public function __construct()
    {
        if (!$this->getMachineName()) {
            throw new \RuntimeException(
                sprintf('Machine name is required: %s', static::class)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        // Final module can override this method to add event listener/subscriber.
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
        // Load routes from YAML file
        if (null !== $this->routeFile) {
            $this->routeFile = true === $this->routeFile ? '%dir/resources/config/routing.yml' : $this->routeFile;
            $path = str_replace('%dir', $this->getPath(), $this->routeFile);
            $loader = new YamlFileLoader(new FileLocator([dirname($path)]));
            $collection = $loader->load('routing.yml');
            $collection->addPrefix($this->routePrefix);
            $app['routes']->addCollection($collection);
        }
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

    /**
     * @return string[]
     */
    public function getRequires()
    {
        return $this->requires;
    }

    /**
     * Define entity mappings.
     *
     * @param Container $c
     * @return array
     */
    public function getEntityMappings(Container $c = null)
    {
        $mappings = [];

        $dir = $this->getPath() . '/models';
        if (is_dir($dir)) {
            $mappings[] = [
                'type'      => 'annotation',
                'namespace' => $this->getNamespace() . '\\models',
                'path'      => $dir,
            ];
        }

        return $mappings;
    }

    /**
     * Get class name from magic service.
     *
     * - [ctrl, home] -> MODULE_NAMESPACE\controllers\HomeController
     * - [cmd, cron]  -> MODULE_NAMESPACE\commands\CronCommand
     *
     * @param string $group
     * @param string $chunks
     * @return string
     */
    public function getMagicServiceClass($group, $chunks)
    {
        $ns = $this->getNamespace();

        $chunks = explode('.', $chunks);
        $last = count($chunks) - 1;
        $chunks[$last] = ucfirst($chunks[$last]);
        $chunks = implode('\\', $chunks);

        switch ($group) {
            case 'ctrl':
                return "{$ns}\\controllers\\{$chunks}Controller";
            case 'cmd':
                return "{$ns}\\commands\\{$chunks}Command";
            default:
                throw new \RuntimeException(sprintf('Invalid magic service: %s', $name));
        }
    }

    /**
     * Method to build module's assets to make them accessible by user's request.
     *
     * @TODO If module public = app public. Don't do anything?
     */
    public function buildAssets($docRoot)
    {
        $target = $docRoot . '/assets/modules/' . $this->getMachineName();
        $candidates = [
            $this->getPath() . '/resources/public',
            $this->getPath() . '/public',
        ];

        foreach ($candidates as $source) {
            if (is_dir($source) && !is_dir($target)) {
                passthru(sprintf("mkdir -p %s", dirname($target)));
                passthru("ln -s '$source' '$target'");
            }
        }
    }

}
