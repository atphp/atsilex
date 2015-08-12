<?php

namespace atsilex\module\system\providers;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Pimple\Container;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\HttpKernel\Profiler\FileProfilerStorage;

class Register
{

    /** @var bool */
    private $isModular;

    /** @var array[] */
    protected $ormMappings = [];

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

    public function __construct($isModular)
    {
        $this->isModular = $isModular;
    }

    public function register(Container $c)
    {
        // Register app core & popular contributed providers
        $c->register(new FormServiceProvider());
        $c->register(new HttpFragmentServiceProvider());
        $c->register(new JmsSerializerServiceProvider(), ['serializer.cacheDir' => $c['app.root'] . '/files/cache/jms.serializer']);
        $c->register(new LocaleServiceProvider());
        $c->register(new RoutingServiceProvider());
        $c->register(new ServiceControllerServiceProvider());
        $c->register(new SessionServiceProvider(), [
            'session.test'              => isset($c['session.test']) ? $c['session.test'] : false,
            'session.storage.save_path' => $c['app.root'] . '/files/session'
        ]);
        $c->register(new SecurityServiceProvider(), ['security.firewalls' => $c['security.firewalls']]);
        $c->register(new TranslationServiceProvider());
        $c->register(new ValidatorServiceProvider());

        $this->registerTwigServices($c);
        $this->registerDoctrineServices($c);
        $this->registerMagicServices($c);

        $c->register(new WebProfilerServiceProvider(), [
            'profiler.storage' => function (Container $c) {
                return new FileProfilerStorage('file:' . $c['app.root'] . '/files/cache/profiler');
            },
        ]);
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

        $c->extend('twig', function (\Twig_Environment $twig, Container $c) {
            $twig->addGlobal('app', $c);
            return $twig;
        });

        $this->isModular && $c->extend(
            'twig.loader.filesystem',
            function (\Twig_Loader_Filesystem $loader, Container $c) {
                /** @var ModularTrait $c */
                foreach ($c->getModules() as $name) {
                    $path = $c->getModulePath($name) . '/resources/views';

                    if (is_dir($path)) {
                        $loader->addPath($path, $name);
                    }
                }

                return $loader;
            }
        );
    }

    private function registerDoctrineServices(Container $c)
    {
        $c->register(new DoctrineServiceProvider(), ['db.options' => isset($c['db.options']) ? $c['db.options'] : []]);

        $c['cache'] = function (Container $c) {
            return new FilesystemCache($c['app.root'] . '/files/cache');
        };

        $c['orm.default_cache'] = function (Container $c) {
            return $c->getCache();
        };

        if ($this->isModular) {
            foreach ($c->getModules() as $module) {
                // Register entity mappings if available
                if ($mappings = $c->getModule($module)->getEntityMappings($c)) {
                    $this->ormMappings = array_merge($this->ormMappings, $mappings);
                }
            }
        }

        $c->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $c['app.root'] . '/files/proxies',
            'orm.em.options'  => [
                'mappings' => $this->ormMappings
            ],
        ]);
    }

    /**
     * @TODO Cache me.
     */
    private function registerMagicServices(Container $c)
    {
        if (!$this->isModular) {
            return;
        }

        /** @var ModularTrait $c */
        foreach ($c->getModules() as $module) {
            foreach ($this->magics as $magic) {
                list($ns, $suffix, $short) = $magic;

                if (($dir = $c->getModulepath($module) . '/' . $ns) && is_dir($dir)) {
                    $this->registerMagicModuleServices($c, $module, $ns, $dir, $suffix, $short);
                }
            }
        }
    }

    private function registerMagicModuleServices(Container $c, $module, $ns, $dir, $suffix, $short)
    {
        // Scan all module's classes, create services for each.
        foreach (glob("{$dir}/*{$suffix}.php") as $file) {
            $class = str_replace([$dir . '/', $suffix . '.php'], '', $file);
            $service = "@{$module}.{$short}." . $this->convertFromCamelCaseToSnakeCase($class);

            $c[$service] = function ($c) use ($module, $class, $ns, $suffix) {
                $moduleNS = $c->getModuleNamespace($module);
                $class = $moduleNS . '\\' . $ns . '\\' . $class . $suffix;

                return new $class($c);
            };
        }
    }

    private function convertFromCamelCaseToSnakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

}
