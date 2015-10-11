<?php

namespace atsilex\module;

use atsilex\module\system\ModularApp;
use Pimple\Container;
use Silex\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * A module can:
 *
 * - Provide/override services.
 * - Listen to system event's
 * - Provide new routes.
 */
abstract class Module extends BaseModule
{
    /** @var bool|string */
    protected $routeFile = false;

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        if (!empty($this->routeFile)) {
            $this->loadRoutes($app['routes']);
        }

        parent::boot($app);
    }

    /**
     * Load routes from YAML file.
     */
    protected function loadRoutes($routes)
    {
        $this->routeFile = (true === $this->routeFile) ? '%dir/resources/config/routing.yml' : $this->routeFile;
        $path = str_replace('%dir', $this->getPath(), $this->routeFile);
        $loader = new YamlFileLoader(new FileLocator([dirname($path)]));
        $collection = $loader->load('routing.yml');
        $collection->addPrefix($this->routePrefix);
        $routes->addCollection($collection);
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
                if (is_link($target)) {
                    $cmds[] = "unlink -f '$target'";
                }
                $cmds[] = sprintf("mkdir -p %s", dirname($target));
                $cmds[] = "ln -s '$source' '$target'";
                passthru(implode('; ', $cmds));
            }
        }
    }
}
