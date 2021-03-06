<?php

namespace atsilex\module\system\providers;

use atsilex\module\system\events\AppEvent;
use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use atsilex\module\system\traits\ModularAppTrait;
use Boris\Boris;
use Doctrine\Common\Cache\FilesystemCache;
use Pimple\Container;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;

class Register
{
    /**
     * Auto register services for modules, so they have not to do.
     *
     * The pattern fo each element:
     *
     *      [sub_namespace, Suffix, short]
     *
     * @see self::registerMagicServices()
     * @var array[]
     */
    protected $magics = [
        # @module.ctrl.service -> module_namespace\controllers\ServiceController
        ['controllers', 'Controller', 'ctrl'],
        # @module.cmd.my.cron -> module_namespace\commands\my\CronCommand
        ['commands', 'Command', 'cmd']
    ];

    public function register(Container $c)
    {
        // Register app core & popular contributed providers
        $c->register(new JmsSerializerServiceProvider(), ['serializer.cacheDir' => $c['app.root'] . '/files/cache/jms.serializer']);

        $this->registerTwigServices($c);
        $this->registerCacheServices($c);
        $this->registerMagicServices($c);
    }

    private function registerCacheServices(Container $c)
    {
        $c->register(new DoctrineCacheServiceProvider(), ['orm.default_cache' => $c['cache.default']]);

        $c['cache'] = function (Container $c) {
            return $c['orm.cache.locator']('default', 'default', $c['cache.default']);
        };
    }

    /**
     * @TODO Make sure the twig templates are cached, configurable.
     */
    private function registerTwigServices(Container $c)
    {
        if (!isset($c['twig'])) {
            $paths = [];

            if (isset($c['twig.path'])) {
                if (is_array($c['twig.path'])) {
                    $paths = array_merge($c['twig.path'], $paths);
                }
                else {
                    $paths[] = $c['twig.path'];
                }
            }

            $paths[] = dirname(__DIR__) . '/resources/default-app/views';

            $c->register(new TwigServiceProvider(), [
                'twig.path'           => $paths,
                'twig.form.templates' => $c['twig.form.templates']
            ]);
        }

        $c->extend('twig', function (\Twig_Environment $twig, ModularApp $c) {
            # @TODO: Document this event.
            $c->getDispatcher()->dispatch(SystemModule::EVENT_TWIG_CREATE, new AppEvent($c, $twig));

            # @TODO: Check $c['twig.app_variable']
            $twig->addGlobal('app', $c);

            return $twig;
        });

        $c->extend('twig.loader.filesystem', function (\Twig_Loader_Filesystem $loader, Container $c) {
            /** @var ModularApp $c */
            foreach ($c->getModules() as $name) {
                $path = $c->getModulePath($name) . '/resources/views';

                if (is_dir($path)) {
                    $loader->addPath($path, $name);
                }
            }

            return $loader;
        });
    }

    private function registerMagicServices(ModularApp $c)
    {
        foreach ($this->findMagicServicesInfo($c) as $service => $info) {
            list($module, $class, $ns, $suffix) = $info;

            $c[$service] = function ($c) use ($module, $class, $ns, $suffix) {
                $moduleNS = $c->getModuleNamespace($module);
                $class = str_replace('/', '\\', $moduleNS . '\\' . $ns . '\\' . $class . $suffix);

                return new $class($c);
            };
        }
    }

    private function findMagicServicesInfo(ModularApp $c)
    {
        $cache = $c->getCache();
        $cache->setNamespace('@system');
        $cacheId = 'services:magic';
        $caching = isset($c['cache.magic_services']) && !empty($c['cache.magic_services']);

        if ($caching && $cache->contains($cacheId)) {
            return $cache->fetch($cacheId);
        }

        $mappings = [];
        foreach ($c->getModules() as $module) {
            foreach ($this->magics as $magic) {
                list($ns, $suffix, $short) = $magic;

                if (($dir = $c->getModulepath($module) . '/' . $ns) && is_dir($dir)) {
                    $this->findModuleMagicServicesInfo($mappings, $module, $ns, $dir, $suffix, $short);
                }
            }
        }

        if ($caching) {
            $cache->save($cacheId, $mappings);
        }

        return $mappings;
    }

    private function findModuleMagicServicesInfo(array &$mappings, $module, $ns, $dir, $suffix, $short)
    {
        // Scan all module's classes, create services for each.
        foreach ($this->rGlob("{$dir}/*{$suffix}.php") as $file) {
            $_short = $short;
            $className = $class = str_replace([$dir . '/', $suffix . '.php'], '', $file);

            if (strpos($className, '/')) {
                $tmp = explode('/', $className);
                $className = array_pop($tmp);
                $_short .= '.' . implode('.', $tmp);
            }

            $service = "{$module}.{$_short}." . $this->convertFromCamelCaseToSnakeCase($className);
            $mappings[$service] = [$module, $class, $ns, $suffix];
        }
    }

    private function rGlob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rGlob($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }

    private function convertFromCamelCaseToSnakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
